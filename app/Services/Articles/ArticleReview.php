<?php

namespace App\Services\Articles;

use App\Models\ArticleGeneration;
use App\Support\PostContent;
use App\Support\Url;
use Illuminate\Support\Str;

class ArticleReview
{
    public function check(ArticleGeneration $generation): array
    {
        $result = $generation->result;
        $html = $result['article_html'];
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $keyword = Str::lower(trim($generation->input['keyword']));
        $warnings = [];
        $checks = [
            'Từ khóa chính trong tiêu đề SEO' => str_contains(Str::lower($result['seo_title']), $keyword),
            'Mở đầu đề cập chủ đề chính' => str_contains(Str::lower(mb_substr($text, 0, 700)), $keyword),
            'Có ít nhất ba đề mục' => preg_match_all('/<h[23]\b/i', $html) >= 3,
            'Mô tả tìm kiếm đủ rõ (120–170 ký tự)' => mb_strlen($result['meta_description']) >= 120 && mb_strlen($result['meta_description']) <= 170,
            'Có nguồn tìm kiếm trực tiếp' => (bool) ($generation->research['grounded'] ?? false),
        ];
        foreach ($checks as $label => $passed) {
            if (! $passed) {
                $warnings[] = $label.': cần xem lại.';
            }
        }
        $allowedLinks = array_merge(array_column($generation->research['sources'] ?? [], 'url'), array_column($generation->research['existing_articles'] ?? [], 'url'));
        preg_match_all('/https?:\/\/[^\s<>"\']+/u', $generation->input['facts'] ?? '', $factLinks);
        $allowedLinks = array_merge($allowedLinks, $factLinks[0]);
        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $links);
        foreach (array_unique($links[1]) as $link) {
            if (! in_array(html_entity_decode($link), $allowedLinks, true)) {
                $warnings[] = 'Liên kết chưa có trong nguồn tham khảo: '.$link;
            }
        }
        if (preg_match('/\b\d[\d.,]*\s*(triệu|tỷ|%|km|kW|kWh)/iu', $text)) {
            $warnings[] = 'Có số liệu giá/thông số/ưu đãi: đối chiếu nguồn chính thức, điều kiện và ngày hiệu lực trước khi đăng.';
        }
        $duplicates = [];
        $shingles = $this->shingles($text);
        foreach (catalog_model('post')::published()->latest('published_at')->limit(100)->get(['title', 'slug', 'sections']) as $post) {
            $existing = $this->shingles(strip_tags(PostContent::fromSections($post->sections ?? [])));
            $overlap = count(array_intersect_key($shingles, $existing));
            $similarity = $overlap / max(1, count($shingles));
            if ($similarity > 0.3 || Str::lower($post->title) === Str::lower($generation->input['title'])) {
                $duplicates[] = ['title' => $post->title, 'url' => Url::absolute('post', $post->slug)];
            }
        }
        if ($duplicates !== []) {
            $warnings[] = 'Có bài cùng tiêu đề hoặc nội dung gần giống trong 100 bài gần nhất. Cân nhắc cập nhật bài cũ.';
        }

        return [
            'checks' => $checks, 'warnings' => $warnings, 'similar_articles' => $duplicates,
            'requires_editor' => true, 'reviewed_at' => now()->toIso8601String(),
            'note' => 'Đây là kiểm tra cấu trúc, không phải xác minh sự thật, kiểm tra đạo văn toàn Internet hoặc điểm xếp hạng Google. Biên tập viên cần đối chiếu nguồn và đọc duyệt toàn bài.',
        ];
    }

    private function shingles(string $text): array
    {
        $words = preg_split('/\s+/u', Str::lower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text)), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        for ($i = 0; $i <= count($words) - 5; $i++) {
            $result[implode(' ', array_slice($words, $i, 5))] = true;
        }

        return $result;
    }
}
