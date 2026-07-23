<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

class FormField extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'rules'   => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(catalog_model('form'));
    }

    /** @return array<int, mixed> */
    public function validationRules(): array
    {
        $rules = $this->rules ?: ['nullable'];

        $rules[] = match ($this->type) {
            'email'    => 'email',
            'tel'      => 'string',
            'date'     => 'date',
            'checkbox' => 'array',
            default    => 'string',
        };

        if (in_array($this->type, ['select', 'radio'], true) && filled($this->options)) {
            $rules[] = Rule::in(array_keys($this->options));
        }

        return array_values(array_unique($rules, SORT_REGULAR));
    }
}
