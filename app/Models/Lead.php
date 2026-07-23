<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'utm'  => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(catalog_model('form'));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(catalog_model('product'));
    }
}
