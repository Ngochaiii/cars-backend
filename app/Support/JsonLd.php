<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

/**
 * Dựng structured data (schema.org) cho từng loại trang. Frontend nhúng vào
 * <script type="application/ld+json"> để Google hiểu nội dung.
 *
 * Trả về mảng — frontend tự json_encode, hoặc dùng ::script() cho sẵn thẻ.
 */
class JsonLd
{
    /** @return array<string, mixed> */
    public static function forProduct(Model $product): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->tagline ?? data_get($product->seo, 'description'),
            'url' => data_get($product->seo, 'canonical') ?: Url::absolute('product', $product->slug),
        ];

        if ($image = data_get($product->seo, 'image') ?: data_get($product->hero, 'src')) {
            $data['image'] = Url::asset($image);
        }

        // Có giá thì thêm Offer để hiện giá trên kết quả tìm kiếm
        if (filled($product->price_from)) {
            $data['offers'] = [
                '@type' => 'Offer',
                'price' => (string) $product->price_from,
                'priceCurrency' => 'VND',
                'availability' => 'https://schema.org/InStock',
            ];
        }

        return array_filter($data);
    }

    /** @return array<string, mixed> */
    public static function forPost(Model $post): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'url' => data_get($post->seo, 'canonical') ?: Url::absolute('post', $post->slug),
            'image' => Url::asset(data_get($post->seo, 'image') ?: $post->cover),
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
        ]);
    }

    /** @return array<string, mixed> */
    public static function forBreadcrumb(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn (array $item, int $i): array => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ], $items, array_keys($items)),
        ];
    }

    /** @return array<string, mixed> */
    public static function organization(): array
    {
        $name = config('catalog.seo.organization.name') ?? Setting::get('site_name');
        $logo = config('catalog.seo.organization.logo') ?? Setting::get('logo');

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $name,
            'url' => rtrim(config('app.url'), '/'),
            'logo' => Url::asset($logo),
            'sameAs' => array_values(array_filter(
                config('catalog.seo.organization.sameAs')
                    ?: [Setting::get('facebook'), Setting::get('youtube'), Setting::get('tiktok')]
            )),
        ]);
    }

    /** Bọc sẵn trong thẻ script để nhúng thẳng vào <head>. */
    public static function script(array $data): string
    {
        return '<script type="application/ld+json">'
            .json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            .'</script>';
    }
}
