<?php

use App\Support\Catalog;
use App\Support\Media;
use App\Support\Money;

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

if (! function_exists('catalog_setting')) {
    /** catalog_setting('hotline') — giá trị màn hình Cài đặt, có cache. */
    function catalog_setting(string $key, mixed $default = null): mixed
    {
        return Catalog::model('setting')::get($key, $default);
    }
}

if (! function_exists('catalog_menu')) {
    /**
     * catalog_menu('header') → các mục gốc kèm con cháu, đã sắp thứ tự.
     * Menu không có thì trả collection rỗng — layout không cần @if.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\MenuItem>
     */
    function catalog_menu(string $key): \Illuminate\Support\Collection
    {
        return Catalog::menu($key);
    }
}

if (! function_exists('catalog_image')) {
    /** catalog_image('catalog/sections/a.webp') → URL đầy đủ. Link ngoài trả nguyên. */
    function catalog_image(mixed $path): ?string
    {
        return Media::url($path);
    }
}

if (! function_exists('catalog_money')) {
    /** catalog_money(5990000000) → "5.990.000.000 đ" */
    function catalog_money(mixed $amount): ?string
    {
        return Money::format($amount);
    }
}
