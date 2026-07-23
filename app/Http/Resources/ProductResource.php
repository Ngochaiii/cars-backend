<?php

namespace App\Http\Resources;

use App\Support\JsonLd;
use App\Support\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'slug'       => $this->slug,
            'name'       => $this->name,
            'tagline'    => $this->tagline,
            'price_from' => $this->price_from,
            'hero'       => $this->hero,
            'category'   => CategoryResource::make($this->whenLoaded('category')),

            'highlights' => $this->when(catalog_feature('highlights'), fn () => $this->highlights ?? []),
            'variants'   => $this->when(
                catalog_feature('variants'),
                fn () => ProductVariantResource::collection($this->whenLoaded('variants'))
            ),
            'options'    => $this->when(
                catalog_feature('options'),
                fn () => ProductOptionResource::collection($this->whenLoaded('options'))
            ),

            // Đã bỏ field trống — frontend không phải kiểm tra isset()
            'sections'   => $this->renderableSections(),
            'specs'      => $this->when(catalog_feature('specs'), fn () => $this->specs ?? []),

            'seo'          => $this->seo,
            'canonical'    => Url::absolute('product', $this->slug),
            'jsonld'       => JsonLd::forProduct($this->resource),
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
