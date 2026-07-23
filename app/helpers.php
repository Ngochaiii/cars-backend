<?php

use App\Support\Catalog;

if (! function_exists('catalog_model')) {
    /**
     * Core không hardcode Product::class — model tra qua config để dự án
     * nào cần thêm hành vi thì extend rồi trỏ lại.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    function catalog_model(string $key): string
    {
        return Catalog::model($key);
    }
}

if (! function_exists('catalog_label')) {
    /** catalog_label('product.plural') → "Dòng xe" */
    function catalog_label(string $key, ?string $default = null): string
    {
        return Catalog::label($key, $default);
    }
}

if (! function_exists('catalog_feature')) {
    /** catalog_feature('dealers') → true|false */
    function catalog_feature(string $key): bool
    {
        return Catalog::feature($key);
    }
}
