<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Setting;
use App\Support\JsonLd;
use Tests\TestCase;

/**
 * Khối tư vấn viên cuối bài: do template dựng từ Cài đặt, không phải chữ do AI
 * viết trong từng bài — tránh 50 bài có 50 đoạn na ná nhau.
 */
class ArticleAdvisorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://vinfast-bacgiang.com']);
        Setting::put('site_name', 'VinFast Bắc Giang');
        Setting::put('advisor_name', 'Nguyễn Hữu Lập');
        Setting::put('advisor_role', 'Tư vấn bán hàng');
        Setting::put('advisor_phone', '0988 123 456');
        Setting::put('advisor_zalo', '0988123456');
    }

    private function article(): Post
    {
        return Post::create([
            'title' => 'Chi phí nuôi xe VinFast VF 7 một năm',
            'slug' => 'chi-phi-nuoi-xe-vf-7',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'excerpt' => 'Tóm tắt.',
            'sections' => [['type' => 'text', 'body' => '<p>Nội dung bài.</p>']],
        ]);
    }

    public function test_cuoi_bai_co_khoi_tu_van_vien_voi_nut_goi_va_zalo(): void
    {
        $html = $this->get(route('posts.show', $this->article()->slug))->assertOk()->getContent();

        $this->assertStringContainsString('article-advisor', $html);
        $this->assertStringContainsString('Nguyễn Hữu Lập', $html);
        $this->assertStringContainsString('Tư vấn bán hàng', $html);
        $this->assertStringContainsString('href="tel:0988123456"', $html);
        $this->assertStringContainsString('https://zalo.me/0988123456', $html);
    }

    public function test_khoi_tu_van_dung_sau_phan_hoi_dap(): void
    {
        $post = $this->article();
        $post->update(['sections' => array_merge($post->sections, [
            ['type' => 'faq', 'title' => 'Câu hỏi thường gặp', 'items' => [
                ['question' => 'Sạc ở đâu?', 'answer' => 'Có nhiều điểm sạc công cộng.'],
            ]],
        ])]);

        $html = $this->get(route('posts.show', $post->slug))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, 'article-advisor'),
            strpos($html, 'Câu hỏi thường gặp'),
            'Khối tư vấn viên phải nằm sau phần hỏi đáp.',
        );
    }

    public function test_tat_khoi_tu_van_thi_bai_khong_con_hien(): void
    {
        Setting::put('advisor_off', '1');

        $html = $this->get(route('posts.show', $this->article()->slug))->assertOk()->getContent();

        $this->assertStringNotContainsString('article-advisor', $html);
    }

    public function test_chua_khai_so_dien_thoai_thi_khong_hien_nut_goi(): void
    {
        Setting::put('advisor_phone', '');
        Setting::put('hotline', '');

        $html = $this->get(route('posts.show', $this->article()->slug))->assertOk()->getContent();

        $this->assertStringNotContainsString('href="tel:"', $html);
    }

    public function test_tu_van_vien_khong_bi_khai_la_tac_gia_bai_viet(): void
    {
        $article = collect(JsonLd::forPost($this->article())['@graph'])->firstWhere('@type', 'Article');

        // Bài do AI viết: tác giả phải là đại lý, không được gán cho người thật.
        $this->assertSame('VinFast Bắc Giang', $article['author']['name']);
        $this->assertStringNotContainsString('Nguyễn Hữu Lập', json_encode($article, JSON_UNESCAPED_UNICODE));
    }
}
