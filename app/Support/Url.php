<?php

namespace App\Support;

/**
 * Một chỗ duy nhất dựng đường dẫn frontend từ config('catalog.routes').
 * Đổi hình dạng URL của một hãng chỉ sửa config, không đụng code.
 */
class Url
{
    /** Tiền tố của một loại, VD 'product' → '/san-pham'. */
    public static function prefix(string $type): string
    {
        return rtrim((string) config("catalog.routes.{$type}", ''), '/');
    }

    /** Đường dẫn tương đối tới một bản ghi theo slug. */
    public static function to(string $type, string $slug): string
    {
        $prefix = static::prefix($type);

        return $prefix === '' ? '/'.$slug : $prefix.'/'.$slug;
    }

    /** Đường dẫn tuyệt đối (kèm domain) — dùng cho sitemap, canonical, JSON-LD. */
    public static function absolute(string $type, string $slug): string
    {
        return rtrim(config('app.url'), '/').static::to($type, $slug);
    }
}
