<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price'          => 'decimal:2',
            'price_original' => 'decimal:2',
            'is_default'     => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(catalog_model('product'));
    }
}
