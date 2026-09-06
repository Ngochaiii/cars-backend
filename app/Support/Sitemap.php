<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Sinh sitemap.xml từ các bản đã publish. Không cần package ngoài —
 * cấu trúc sitemap là XML thuần, và tự viết thì không phụ thuộc phiên bản
 * PHP mà spatie/laravel-sitemap đòi (8.4).
 */
class Sitemap
{
    /** @return array<int, array{loc: string, lastmod: ?string}> */
    public function urls(): array
    {
        $urls = $this->staticUrls();

        foreach ((array) config('catalog.seo.sitemap_includes', []) as $type) {
            $urls = [...$urls, ...$this->urlsFor($type)];
        }

        return $urls;
    }

    /** @return array<int, array{loc: string, lastmod: null}> */
    protected function staticUrls(): array
    {
        return collect((array) config('catalog.seo.sitemap_routes', []))
            ->filter(fn (string $name): bool => $this->staticRouteIsAvailable($name))
            ->map(fn (string $name): array => [
                'loc' => Url::route($name),
                'lastmod' => null,
            ])
            ->values()
            ->all();
    }

    protected function staticRouteIsAvailable(string $name): bool
    {
        if (! Route::has($name)) {
            return false;
        }

        return match ($name) {
            'booking' => Catalog::feature('forms')
                && Catalog::query('form')
                    ->whereIn('key', (array) config('catalog.frontend.booking.forms', []))
                    ->where('is_active', true)
                    ->exists(),
            'accessories' => filled($slug = config('catalog.frontend.accessory_category'))
                && Catalog::query('category')->where('slug', $slug)->exists(),
            default => true,
        };
    }

    public function toXml(): string
    {
        $xml = new \XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->urls() as $url) {
            $xml->startElement('url');
            $xml->writeElement('loc', $url['loc']);
            if ($url['lastmod']) {
                $xml->writeElement('lastmod', $url['lastmod']);
            }
            $xml->endElement();
        }

        $xml->endElement();

        return $xml->outputMemory();
    }

    /** @return array<int, array{loc: string, lastmod: ?string}> */
    protected function urlsFor(string $type): array
    {
        $modelKey = match ($type) {
            'category' => 'category',
            'post' => 'post',
            'page' => 'page',
            default => 'product',
        };

        $query = Catalog::query($modelKey);

        // Danh mục phụ kiện có URL public riêng và route danh mục chỉ
        // redirect 301 sang URL đó. Sitemap không được liệt kê URL redirect.
        if ($type === 'category'
            && Route::has('accessories')
            && filled($slug = config('catalog.frontend.accessory_category'))) {
            $query->where('slug', '!=', $slug);
        }

        // Product/Post có lịch đăng nên phải dùng đúng scope published,
        // tránh làm lộ URL hẹn giờ trong sitemap. Page không có published_at.
        if (in_array($type, ['product', 'post'], true)) {
            $query->published();
        } elseif ($type === 'page') {
            $query->where('status', 'published');
        }

        return $query
            ->get()
            ->map(fn ($model): array => [
                'loc' => Url::absolute($type, $model->slug),
                'lastmod' => $model->updated_at?->toAtomString(),
            ])
            ->all();
    }
}
