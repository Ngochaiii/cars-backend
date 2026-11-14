<?php

namespace App\Services\Articles;

use App\Models\ArticleGeneration;
use App\Support\PostContent;
use App\Support\Url;
use Illuminate\Support\Str;

class ArticleReview
{
    /**
     * Hư từ và từ để hỏi trong từ khóa: câu trả lời tự nhiên hiếm khi nhắc lại
     * chúng ("nên chọn màu nào" → "nên chọn màu trung tính"), nên bắt buộc có
     * mặt là báo nhầm.
     */
    private const FUNCTION_WORDS = [
        'nào', 'gì', 'sao', 'thế', 'bao', 'nhiêu', 'không', 'chưa', 'hay',
        'là', 'và', 'của', 'cho', 'với', 'tại', 'khi', 'để', 'nên', 'được',
        'các', 'những', 'một', 'này', 'đó', 'thì', 'mà', 'ra', 'vào', 'như',
    ];

    public function check(ArticleGeneration $generation): array
    {
        $result = $generation->result;
        $html = $result['article_html'];
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Từ khóa chính do bước nghiên cứu rút ra từ tiêu đề; tiêu đề là mức
        // dự phòng cho các yêu cầu cũ chưa có bản nghiên cứu.
        $keyword = Str::lower(trim((string) (
            $generation->research['brief']['keyword']
            ?? $generation->input['keyword']
            ?? $generation->input['title']
        )));
        $warnings = [];
        $words = count(preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: []);
        $checks = [
            'Từ khóa chính trong tiêu đề SEO' => $this->covers($result['seo_title'], $keyword),
            // Trợ lý AI trích đoạn đầu tiên, nên câu trả lời phải nằm ngay đó —
            // không phải sau một đoạn dẫn chung về thị trường.
            'Mở đầu trả lời ngay chủ đề chính' => $this->covers(mb_substr($text, 0, 300), $keyword),
            'Phần lớn đề mục là câu hỏi' => $this->questionHeadings($html),
            'Có ít nhất ba đề mục' => preg_match_all('/<h[23]\b/i', $html) >= 3,
            'Mô tả tìm kiếm đủ rõ (120–170 ký tự)' => mb_strlen($result['meta_description']) >= 120 && mb_strlen($result['meta_description']) <= 170,
            'Bài đủ dài (từ 1.200 từ)' => $words >= 1200,
            'Thẻ title viết hoa theo lối tiếng Việt' => $this->sentenceCase($result['seo_title']),
            'Có nguồn tìm kiếm trực tiếp' => (bool) ($generation->research['grounded'] ?? false),
        ];
        foreach ($checks as $label => $passed) {
            if (! $passed) {
                $warnings[] = $label.': cần xem lại.';
            }
        }
        $allowedLinks = array_merge(
            array_column($generation->research['sources'] ?? [], 'url'),
            array_column($generation->research['existing_articles'] ?? [], 'url'),
            array_column($generation->research['products'] ?? [], 'url'),
        );
        preg_match_all('/https?:\/\/[^\s<>"\']+/u', $generation->input['facts'] ?? '', $factLinks);
        $allowedLinks = array_merge($allowedLinks, $factLinks[0]);
        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $links);
        foreach (array_unique($links[1]) as $link) {
            if (! in_array(html_entity_decode($link), $allowedLinks, true)) {
                $warnings[] = 'Liên kết chưa có trong nguồn tham khảo: '.$link;
            }
        }
        // Liên kết nội bộ nhiều quá thì loãng đích chuyển đổi và trông như nhồi link.
        if (count(array_unique($links[1])) > 5) {
            $warnings[] = 'Quá nhiều liên kết trong bài ('.count(array_unique($links[1])).'). Giữ lại liên kết tới trang xe chính và vài bài liên quan, bỏ phần còn lại.';
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

    /** Đề mục dạng câu hỏi khớp truy vấn tìm kiếm tốt hơn đề mục dạng khẳng định. */
    private function questionHeadings(string $html): bool
    {
        preg_match_all('/<h2\b[^>]*>(.*?)<\/h2>/is', $html, $matches);
        $headings = array_map(fn (string $heading): string => trim(strip_tags($heading)), $matches[1]);
        $headings = array_values(array_filter($headings));

        if ($headings === []) {
            return false;
        }

        $questions = array_filter($headings, fn (string $heading): bool => str_ends_with($heading, '?'));

        return count($questions) / count($headings) >= 0.6;
    }

    /**
     * Từ khóa chính hiếm khi xuất hiện nguyên cụm trong câu tiếng Việt tự
     * nhiên ("VF 5 Plus chạy dịch vụ Bắc Giang" → "VinFast VF 5 Plus có phù
     * hợp chạy dịch vụ ở Bắc Giang không?"). Đủ mọi từ có nghĩa là đạt.
     */
    private function covers(string $haystack, string $keyword): bool
    {
        $words = array_filter(
            preg_split('/\s+/u', Str::lower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $keyword)), -1, PREG_SPLIT_NO_EMPTY) ?: [],
            fn (string $word): bool => mb_strlen($word) > 1 && ! in_array($word, self::FUNCTION_WORDS, true),
        );

        if ($words === []) {
            return false;
        }

        $haystack = Str::lower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $haystack));

        foreach ($words as $word) {
            if (! str_contains($haystack, $word)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tiếng Việt không viết hoa từng chữ như tiêu đề tiếng Anh. Bỏ qua từ đầu
     * câu, tên riêng viết hoa giữa chữ (VinFast) và từ viết tắt toàn hoa (VF).
     */
    private function sentenceCase(string $title): bool
    {
        $words = preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $rest = array_slice($words, 1);
        $plain = array_values(array_filter(
            $rest,
            fn (string $word): bool => preg_match('/^\p{L}{3,}$/u', $word) === 1 && $word !== Str::upper($word) && $word === Str::ucfirst(Str::lower($word)),
        ));

        $candidates = array_values(array_filter(
            $rest,
            fn (string $word): bool => preg_match('/^\p{L}{3,}$/u', $word) === 1 && $word !== Str::upper($word),
        ));

        if (count($plain) < 4) {
            return true;
        }

        return count($plain) / max(1, count($candidates)) < 0.6;
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
