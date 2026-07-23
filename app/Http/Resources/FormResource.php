<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key'             => $this->key,
            'name'            => $this->name,
            'success_message' => $this->success_message,
            'fields'          => $this->whenLoaded('fields', fn () => $this->fields->map(fn ($field) => [
                'key'         => $field->key,
                'label'       => $field->label,
                'type'        => $field->type,
                'options'     => $field->options,
                'placeholder' => $field->placeholder,
                'width'       => $field->width,
                'required'    => in_array('required', $field->rules ?? [], true),
            ])->all()),
        ];
    }
}
