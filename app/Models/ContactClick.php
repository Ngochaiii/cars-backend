<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Một lượt khách bấm nút gọi / Zalo trên web — xem ContactClickController.
 */
class ContactClick extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'source' => 'array',
            'trail'  => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(catalog_model('product'));
    }

    /** "Google Ads" · utm_source · "Vào thẳng / khác" — để cột admin đọc được ngay. */
    public function sourceLabel(): string
    {
        $source = $this->source ?? [];

        if (filled($source['gclid'] ?? null) || filled($source['gad_campaignid'] ?? null)) {
            return 'Google Ads';
        }

        return $source['utm_source'] ?? 'Vào thẳng / khác';
    }

    /** "13:03 /san-pham/vinfast-vf-5-plus → 13:05 /dat-coc" */
    public function trailText(): string
    {
        return collect($this->trail ?? [])
            ->map(fn (array $step) => trim(($step['t'] ?? '').' '.($step['p'] ?? '')))
            ->implode(' → ');
    }
}
