<?php

namespace App\Support;

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
        $urls = [];

        foreach ((array) config('catalog.seo.sitemap_includes', []) as $type) {
            $urls = [...$urls, ...$this->urlsFor($type)];
        }

        return $urls;
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
