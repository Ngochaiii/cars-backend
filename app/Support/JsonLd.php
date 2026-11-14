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

    /**
     * Bài viết khai một @graph: Article + FAQPage (khi bài có khối hỏi đáp) +
     * BreadcrumbList. Trợ lý AI và Google đọc được cả ba trong một thẻ script,
     * và cặp hỏi–đáp là thứ dễ được trích dẫn nguyên văn nhất.
     *
     * @return array<string, mixed>
     */
    public static function forPost(Model $post): array
    {
        $url = data_get($post->seo, 'canonical') ?: Url::absolute('post', $post->slug);
        $publisher = static::organization();

        $article = array_filter([
            '@type' => 'Article',
            '@id' => $url.'#article',
            'headline' => $post->title,
            'description' => data_get($post->seo, 'description') ?: $post->excerpt,
            'url' => $url,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'image' => Url::asset(data_get($post->seo, 'image') ?: $post->cover),
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => ($post->updated_at ?? $post->published_at)?->toAtomString(),
            'inLanguage' => 'vi-VN',
            'keywords' => data_get($post->seo, 'keywords'),
            'articleSection' => $post->category?->name,
            'wordCount' => static::wordCount($post),
            'author' => array_filter(['@type' => $publisher['@type'] ?? 'Organization', 'name' => $publisher['name'] ?? null, 'url' => $publisher['url'] ?? null]),
            'publisher' => ['@id' => $publisher['@id'] ?? null],
        ], fn (mixed $value): bool => filled($value));

        $breadcrumb = static::forBreadcrumb([
            ['name' => 'Trang chủ', 'url' => rtrim(config('app.url'), '/').'/'],
            ['name' => 'Tin tức', 'url' => route('posts.index')],
            ['name' => $post->title, 'url' => $url],
        ]);
        unset($breadcrumb['@context']);

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                $article,
                static::faqPage(static::faqItems($post), $url),
                $breadcrumb,
            ])),
        ];
    }

    /**
     * Cặp hỏi–đáp lấy từ mục `faq` của bài, không phải do AI tự khai trong
     * JSON-LD — khai câu hỏi không có trên trang là vi phạm hướng dẫn của Google.
     *
     * @return array<int, array{question:string,answer:string}>
     */
    public static function faqItems(Model $post): array
    {
        return collect($post->sections ?? [])
            ->where('type', 'faq')
            ->flatMap(fn (array $section): array => $section['items'] ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['question'] ?? null) && filled($item['answer'] ?? null))
            ->map(fn (array $item): array => [
                'question' => trim(strip_tags((string) $item['question'])),
                'answer' => trim(strip_tags((string) $item['answer'])),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{question:string,answer:string}>  $items
     * @return array<string, mixed>|null
     */
    private static function faqPage(array $items, string $url): ?array
    {
        if ($items === []) {
            return null;
        }

        return [
            '@type' => 'FAQPage',
            '@id' => $url.'#faq',
            'mainEntity' => array_map(fn (array $item): array => [
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
            ], $items),
        ];
    }

    private static function wordCount(Model $post): int
    {
        $text = collect($post->sections ?? [])
            ->where('type', 'text')
            ->map(fn (array $section): string => strip_tags((string) ($section['body'] ?? '')))
            ->implode(' ');

        return count(preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: []);
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
            '@type' => config('catalog.seo.organization.type', 'Organization'),
            '@id' => rtrim(config('app.url'), '/').'/#organization',
            'name' => $name,
            'url' => rtrim(config('app.url'), '/'),
            'logo' => Url::asset($logo),
            'image' => Url::asset(Setting::get('social_image') ?: $logo),
            'telephone' => Setting::get('hotline'),
            'email' => Setting::get('email'),
            'address' => Setting::get('address'),
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
