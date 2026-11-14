<?php

namespace App\Services\Articles;

use App\Models\ArticleGeneration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Biến kết quả Gemini thành một bài hoàn chỉnh trong bảng posts: slug, tóm tắt,
 * ảnh bìa, chuyên mục, thân bài, khối hỏi đáp và bộ thẻ SEO. Biên tập viên chỉ
 * nhập tiêu đề nên mọi thứ còn lại phải được điền ở đây, không bắt họ bấm thêm.
 *
 * Bài luôn ở trạng thái Nháp khi tạo mới — không bao giờ tự đăng.
 */
class ArticleDraft
{
    public function apply(ArticleGeneration $generation): Model
    {
        $result = $generation->result ?? [];
        $title = (string) ($generation->input['title'] ?? '');
        $post = $this->target($generation);

        $post->fill([
            'title' => $title,
            'excerpt' => $result['excerpt'] ?? null,
            'cover' => $post->cover ?: $this->cover($title),
            'post_category_id' => $post->post_category_id ?: $this->category($result['category'] ?? null),
            'sections' => $this->sections($post, $result),
            'seo' => $this->seo($post, $result),
            'ai_generation_id' => $generation->id,
            'ai_reviewed' => false,
        ]);

        if (! $post->exists) {
            $post->status = 'draft';
        }

        $post->save();

        return $post;
    }

    /** Viết lại thì cập nhật đúng bài cũ; còn lại tạo bài mới ở trạng thái Nháp. */
    private function target(ArticleGeneration $generation): Model
    {
        $id = $generation->post_id ?? ($generation->input['post_id'] ?? null);
        $model = catalog_model('post');

        return $id ? $model::find($id) ?? new $model : new $model;
    }

    /**
     * Thân bài là một mục văn bản, hỏi đáp là một mục riêng để frontend dựng
     * được FAQPage. Các mục ảnh/video/bảng do người nhập thêm vẫn giữ nguyên.
     *
     * @param  array<string, mixed>  $result
     * @return array<int, array<string, mixed>>
     */
    private function sections(Model $post, array $result): array
    {
        $kept = collect($post->sections ?? [])
            ->reject(fn (array $section): bool => in_array($section['type'] ?? 'media', ['text', 'faq'], true))
            ->values()
            ->all();

        $faq = collect($result['faq'] ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['question'] ?? null) && filled($item['answer'] ?? null))
            ->map(fn (array $item): array => ['question' => (string) $item['question'], 'answer' => (string) $item['answer']])
            ->values()
            ->all();

        return array_values(array_filter([
            filled($result['article_html'] ?? null)
                ? ['type' => 'text', 'body' => (string) $result['article_html']]
                : null,
            $faq !== []
                ? ['type' => 'faq', 'title' => 'Câu hỏi thường gặp', 'items' => $faq]
                : null,
            ...$kept,
        ]));
    }

    /**
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    private function seo(Model $post, array $result): array
    {
        return array_filter(array_merge($post->seo ?? [], [
            'title' => $result['seo_title'] ?? null,
            'description' => $result['meta_description'] ?? null,
            'keywords' => implode(', ', $result['keywords'] ?? []),
        ]), fn (mixed $value): bool => filled($value));
    }

    /**
     * Lấy ảnh hero của dòng xe được nhắc trong tiêu đề. Tên dài khớp trước để
     * "VF 8 The ALL NEW" không bị "VF 8" giành mất.
     */
    private function cover(string $title): ?string
    {
        $haystack = Str::lower($title);

        return catalog_model('product')::query()
            ->get(['name', 'hero'])
            ->sortByDesc(fn (Model $product): int => mb_strlen((string) $product->name))
            ->filter(fn (Model $product): bool => filled($product->name) && str_contains($haystack, Str::lower($product->name)))
            ->map(fn (Model $product): ?string => data_get($product->hero, 'src'))
            ->first(fn (?string $src): bool => filled($src));
    }

    private function category(?string $name): ?int
    {
        if (blank($name)) {
            return null;
        }

        return catalog_model('post_category')::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower(trim($name))])
            ->value('id');
    }
}
