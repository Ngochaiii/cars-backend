<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Một chỗ duy nhất đổi giá trị người nhập gõ vào (ảnh, link video) thành
 * thứ nhúng được vào Blade. Frontend không tự gọi Storage::url() rải rác.
 */
class Media
{
    /**
     * URL của một ảnh. Nhận cả:
     *   - đường dẫn trên disk public: `catalog/sections/a.webp`
     *   - link ngoài: `https://…`
     *   - MẢNG đường dẫn — state của FileUpload là mảng, không phải chuỗi
     */
    public static function url(mixed $path): ?string
    {
        if (is_array($path)) {
            $path = reset($path) ?: null;
        }

        if (blank($path) || ! is_string($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:', '/'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Link YouTube/Vimeo người nhập dán vào → link nhúng iframe được.
     * Link lạ trả nguyên, để nhúng thẳng file mp4 hoặc player khác vẫn chạy.
     */
    public static function embed(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (preg_match('~youtu\.be/([\w-]+)~', $url, $m)
            || preg_match('~youtube\.com/(?:watch\?v=|embed/|shorts/)([\w-]+)~', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return $url;
    }

    /** File video tự host (mp4/webm) thì dùng thẻ <video>, còn lại là <iframe>. */
    public static function isFile(?string $url): bool
    {
        return filled($url) && Str::endsWith(Str::lower(parse_url($url, PHP_URL_PATH) ?? ''), ['.mp4', '.webm', '.ogv']);
    }
}
