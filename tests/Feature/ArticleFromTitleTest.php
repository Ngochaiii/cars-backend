<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Jobs\GenerateArticle;
use App\Models\ArticleGeneration;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use App\Services\Articles\ArticleDraft;
use App\Services\Articles\ArticlePipeline;
use App\Services\Articles\GenerationRequests;
use App\Services\GeminiArticleWriter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Luồng mới: biên tập viên chỉ nhập tiêu đề. Gemini nghiên cứu từ khóa từ
 * tiêu đề rồi tự điền toàn bộ các thẻ của một bài chuẩn SEO/GEO.
 */
class ArticleFromTitleTest extends TestCase
{
    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->editor = User::create(['name' => 'Editor', 'email' => 'editor@test.local', 'password' => 'test']);
        $this->actingAs($this->editor);
        config([
            'services.gemini.key' => 'test-key',
            'services.gemini.model' => 'gemini-3.8-flash',
            'services.gemini.fallback_models' => ['gemini-3.7-flash'],
            'services.gemini.google_search' => false,
        ]);
    }

    private function articleResult(): array
    {
        return [
            'excerpt' => 'Tóm tắt bài viết về xe điện VinFast cho khách Bắc Giang.',
            'seo_title' => 'Đánh giá VinFast VF 3 cho gia đình',
            'meta_description' => str_repeat('Thông tin hữu ích khi tìm hiểu VinFast VF 3. ', 3),
            'keywords' => ['VinFast VF 3', 'xe điện đô thị', 'giá VinFast VF 3'],
            'article_html' => '<p>'.str_repeat('Câu trả lời ngắn cho người đang tìm hiểu VF 3. ', 30).'</p>'
                .'<h2>VinFast VF 3 phù hợp với ai?</h2><h2>Chi phí sử dụng thế nào?</h2><h2>Mua ở Bắc Giang ra sao?</h2>',
            'faq' => [
                ['question' => 'VinFast VF 3 đi được bao xa mỗi lần sạc?', 'answer' => 'Theo công bố của hãng, xe đi được khoảng 210 km mỗi lần sạc đầy.'],
                ['question' => 'Có trạm sạc ở Bắc Giang không?', 'answer' => 'Có nhiều điểm sạc công cộng trong thành phố; đại lý sẽ hướng dẫn vị trí gần nhất.'],
                ['question' => 'Thủ tục lái thử gồm những gì?', 'answer' => 'Khách mang theo giấy phép lái xe và đặt lịch trước với đại lý.'],
            ],
            'category' => 'Trải nghiệm',
        ];
    }

    private function researchResult(): array
    {
        return [
            'keyword' => 'VinFast VF 3',
            'intent' => 'information',
            'intent_analysis' => 'Người đọc muốn tìm hiểu xe trước khi mua.',
            'outline' => ['VF 3 phù hợp với ai', 'Chi phí sử dụng', 'Mua ở Bắc Giang'],
            'related_keywords' => ['giá VF 3', 'xe điện đô thị'],
            'reader_questions' => ['VF 3 đi được bao xa?'],
            'evidence_notes' => 'Thông số cần đối chiếu trang chính thức.',
            'missing_information' => [],
        ];
    }

    private function geminiReturns(array ...$payloads): void
    {
        $sequence = Http::fakeSequence();
        foreach ($payloads as $payload) {
            $sequence->push(['candidates' => [['content' => ['parts' => [['text' => json_encode($payload, JSON_UNESCAPED_UNICODE)]]]]]]);
        }
    }

    public function test_chi_can_tieu_de_la_bat_dau_duoc_yeu_cau_viet_bai(): void
    {
        $generation = app(GenerationRequests::class)->start($this->editor->id, [
            'title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ',
        ]);

        $this->assertSame('Đánh giá VinFast VF 3 cho gia đình nhỏ', $generation->input['title']);
        $this->assertSame('research', $generation->stage);
    }

    public function test_bam_xong_la_thay_ngay_bai_nhap_trong_danh_sach(): void
    {
        $generation = app(GenerationRequests::class)->start($this->editor->id, [
            'title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ',
        ]);

        $post = Post::sole();
        $this->assertSame($post->id, $generation->post_id);
        $this->assertSame('Đánh giá VinFast VF 3 cho gia đình nhỏ', $post->title);
        $this->assertSame('draft', $post->status);
        $this->assertSame($generation->id, $post->ai_generation_id);
        $this->assertNull($post->published_at);
    }

    public function test_bam_hai_lan_khong_tao_hai_bai_nhap(): void
    {
        $service = app(GenerationRequests::class);
        $first = $service->start($this->editor->id, ['title' => 'Đánh giá VinFast VF 3']);
        $second = $service->start($this->editor->id, ['title' => 'Đánh giá VinFast VF 3']);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Post::count());
    }

    public function test_viet_bai_khong_can_anh_bia(): void
    {
        $this->geminiReturns($this->articleResult());

        $result = app(GeminiArticleWriter::class)->generate('Đánh giá VinFast VF 3', null);

        $this->assertSame('Đánh giá VinFast VF 3 cho gia đình', $result['seo_title']);
        Http::assertSent(fn ($request) => ! isset($request->data()['contents'][0]['parts'][0]['inlineData']));
    }

    public function test_gemini_tu_tim_tu_khoa_chinh_tu_tieu_de(): void
    {
        $generation = ArticleGeneration::create([
            'user_id' => $this->editor->id,
            'input' => ['title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ'],
        ]);
        $this->geminiReturns($this->researchResult());

        app(ArticlePipeline::class)->advance($generation);

        $this->assertSame('VinFast VF 3', $generation->fresh()->research['brief']['keyword']);
        $this->assertSame('writing', $generation->fresh()->stage);
    }

    public function test_viet_xong_thi_tu_tao_bai_nhap_day_du_cac_the(): void
    {
        PostCategory::create(['name' => 'Trải nghiệm', 'slug' => 'trai-nghiem']);
        $generation = ArticleGeneration::create([
            'user_id' => $this->editor->id,
            'input' => ['title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ'],
            'stage' => 'review',
            'research' => ['brief' => $this->researchResult(), 'grounded' => false, 'sources' => []],
            'result' => $this->articleResult(),
        ]);

        app(ArticlePipeline::class)->advance($generation);

        $post = Post::firstOrFail();
        $this->assertSame('completed', $generation->fresh()->status);
        $this->assertSame($post->id, $generation->fresh()->post_id);
        $this->assertSame('Đánh giá VinFast VF 3 cho gia đình nhỏ', $post->title);
        $this->assertSame('danh-gia-vinfast-vf-3-cho-gia-dinh-nho', $post->slug);
        $this->assertSame('draft', $post->status);
        $this->assertFalse((bool) $post->ai_reviewed);
        $this->assertSame($generation->id, $post->ai_generation_id);
        $this->assertSame('Đánh giá VinFast VF 3 cho gia đình', data_get($post->seo, 'title'));
        $this->assertStringContainsString('VinFast VF 3', data_get($post->seo, 'description'));
        $this->assertSame('VinFast VF 3, xe điện đô thị, giá VinFast VF 3', data_get($post->seo, 'keywords'));
        $this->assertStringContainsString('VF 3 phù hợp với ai', collect($post->sections)->firstWhere('type', 'text')['body']);
        $this->assertCount(3, collect($post->sections)->firstWhere('type', 'faq')['items']);
        $this->assertSame(PostCategory::first()->id, $post->post_category_id);
    }

    public function test_slug_khong_trung_voi_bai_da_co(): void
    {
        Post::create(['title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ', 'slug' => 'danh-gia-vinfast-vf-3-cho-gia-dinh-nho', 'status' => 'published']);
        $generation = ArticleGeneration::create([
            'user_id' => $this->editor->id,
            'input' => ['title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ'],
            'result' => $this->articleResult(),
            'research' => ['brief' => $this->researchResult()],
        ]);

        $post = app(ArticleDraft::class)->apply($generation);

        $this->assertNotSame('danh-gia-vinfast-vf-3-cho-gia-dinh-nho', $post->slug);
        $this->assertStringStartsWith('danh-gia-vinfast-vf-3-cho-gia-dinh-nho', $post->slug);
    }

    public function test_tu_lay_anh_bia_tu_dong_xe_duoc_nhac_trong_tieu_de(): void
    {
        catalog_model('product')::create([
            'name' => 'VinFast VF 3', 'slug' => 'vinfast-vf-3', 'status' => 'published',
            'hero' => ['type' => 'image', 'src' => 'catalog/hero/vf3.webp'],
        ]);
        $generation = ArticleGeneration::create([
            'user_id' => $this->editor->id,
            'input' => ['title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ'],
            'result' => $this->articleResult(),
            'research' => ['brief' => $this->researchResult()],
        ]);

        $post = app(ArticleDraft::class)->apply($generation);

        $this->assertSame('catalog/hero/vf3.webp', $post->cover);
    }

    public function test_khong_tim_duoc_dong_xe_thi_de_trong_anh_bia(): void
    {
        $generation = ArticleGeneration::create([
            'user_id' => $this->editor->id,
            'input' => ['title' => 'Kinh nghiệm chăm sóc xe điện mùa mưa'],
            'result' => $this->articleResult(),
            'research' => ['brief' => $this->researchResult()],
        ]);

        $this->assertNull(app(ArticleDraft::class)->apply($generation)->cover);
    }

    public function test_tu_danh_sach_chi_nhap_tieu_de_la_xong(): void
    {
        Livewire::test(ListPosts::class)
            ->callAction('writeArticleWithAi', ['title' => 'Đánh giá VinFast VF 3 cho gia đình nhỏ']);

        $generation = ArticleGeneration::sole();
        $this->assertSame('Đánh giá VinFast VF 3 cho gia đình nhỏ', $generation->input['title']);
        $this->assertSame($this->editor->id, $generation->user_id);
        $this->assertNotNull($generation->post_id);
        Queue::assertPushed(GenerateArticle::class, 1);
    }

    public function test_thieu_tieu_de_thi_khong_tao_yeu_cau(): void
    {
        Livewire::test(ListPosts::class)
            ->callAction('writeArticleWithAi', ['title' => ''])
            ->assertHasActionErrors(['title' => 'required']);

        $this->assertSame(0, ArticleGeneration::count());
        $this->assertSame(0, Post::count());
        Queue::assertNothingPushed();
    }

    public function test_viet_lai_bang_ai_gan_voi_bai_dang_sua(): void
    {
        $post = Post::create(['title' => 'Bài cũ cần viết lại', 'slug' => 'bai-cu', 'status' => 'draft']);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction('rewriteArticleWithAi');

        $generation = ArticleGeneration::sole();
        $this->assertSame($post->id, $generation->post_id);
        $this->assertSame('Bài cũ cần viết lại', $generation->input['title']);
    }

    public function test_dang_bai_bang_mot_nut_va_ghi_nhan_da_doc_duyet(): void
    {
        $generation = ArticleGeneration::create(['user_id' => $this->editor->id, 'input' => ['title' => 'Bài AI'], 'status' => 'completed']);
        $post = Post::create(['title' => 'Bài AI', 'slug' => 'bai-ai', 'status' => 'draft', 'ai_generation_id' => $generation->id, 'ai_reviewed' => false]);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction('publishArticle');

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertTrue((bool) $post->ai_reviewed);
        $this->assertNotNull($post->published_at);
    }

    public function test_bai_da_dang_thi_khong_con_nut_dang(): void
    {
        $post = Post::create(['title' => 'Bài đã đăng', 'slug' => 'bai-da-dang', 'status' => 'published', 'published_at' => now()]);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->assertActionHidden('publishArticle');
    }

    public function test_form_bai_viet_khong_con_cac_o_nhap_tay_cho_ai(): void
    {
        $post = Post::create(['title' => 'Bài mẫu', 'slug' => 'bai-mau', 'status' => 'draft']);
        $component = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()]);

        foreach (['ai_keyword', 'ai_related_keywords', 'ai_intent', 'ai_location', 'ai_use_search', 'ai_facts', 'generation_request_id'] as $field) {
            $component->assertFormFieldDoesNotExist($field);
        }
    }

    public function test_viet_lai_cap_nhat_dung_bai_cu_khong_tao_ban_trung(): void
    {
        $post = Post::create(['title' => 'Bài cũ', 'slug' => 'bai-cu', 'status' => 'published']);
        $generation = ArticleGeneration::create([
            'user_id' => $this->editor->id,
            'input' => ['title' => 'Bài cũ', 'post_id' => $post->id],
            'result' => $this->articleResult(),
            'research' => ['brief' => $this->researchResult()],
        ]);

        $updated = app(ArticleDraft::class)->apply($generation);

        $this->assertSame($post->id, $updated->id);
        $this->assertSame('bai-cu', $updated->slug);
        $this->assertSame(1, Post::count());
    }
}
