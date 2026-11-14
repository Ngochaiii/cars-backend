<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\EditPost;
use App\Jobs\GenerateArticle;
use App\Media\MediaStore;
use App\Models\ArticleGeneration;
use App\Models\Post;
use App\Models\User;
use App\Services\Articles\ArticleDraft;
use App\Services\Articles\ArticlePipeline;
use App\Services\Articles\ArticleReview;
use App\Services\Articles\GenerationRequests;
use App\Services\Articles\PublicationReview;
use App\Support\Url;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleGenerationTest extends TestCase
{
    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->editor = User::create(['name' => 'Editor', 'email' => 'editor@test.local', 'password' => 'test']);
        $this->actingAs($this->editor);
        config(['services.gemini.key' => 'test-key', 'services.gemini.model' => 'gemini-3.8-flash', 'services.gemini.fallback_models' => ['gemini-3.7-flash'], 'services.gemini.google_search' => false]);
        app(MediaStore::class)->write('catalog/posts/test.jpg', UploadedFile::fake()->image('cover.jpg')->getContent());
    }

    private function input(): array
    {
        return ['title' => 'Kinh nghiệm xe điện'];
    }

    private function articleResult(): array
    {
        return ['excerpt' => 'Tóm tắt', 'seo_title' => 'Kinh nghiệm xe điện', 'meta_description' => str_repeat('Thông tin xe điện. ', 8), 'keywords' => ['xe điện', 'VinFast', 'lái thử'], 'article_html' => '<p>'.str_repeat('Kinh nghiệm xe điện hữu ích cho người đọc. ', 30).'</p><h2>Chuẩn bị</h2><h2>Lái thử</h2><h2>So sánh</h2>', 'faq' => [['question' => 'Sạc ở đâu?', 'answer' => 'Có nhiều điểm sạc công cộng.']], 'category' => null];
    }

    private function generation(array $attributes = []): ArticleGeneration
    {
        return ArticleGeneration::create(array_merge(['user_id' => $this->editor->id, 'input' => $this->input()], $attributes));
    }

    public function test_repeated_clicks_create_one_persistent_background_request(): void
    {
        $service = app(GenerationRequests::class);
        $first = $service->start($this->editor->id, $this->input());
        $second = $service->start($this->editor->id, $this->input());
        $this->assertSame($first->id, $second->id);
        Queue::assertPushed(GenerateArticle::class, 1);
        Queue::assertPushed(GenerateArticle::class, fn ($job) => $job->connection === 'article_generation' && $job->queue === 'articles');
    }

    public function test_pipeline_saves_each_stage_and_does_not_publish(): void
    {
        $generation = $this->generation();
        Http::fakeSequence()
            ->push(['candidates' => [['content' => ['parts' => [['text' => json_encode(['keyword' => 'xe điện', 'intent' => 'information', 'intent_analysis' => 'Tìm hiểu', 'outline' => ['Chuẩn bị', 'Lái thử', 'So sánh'], 'evidence_notes' => 'Chưa xác minh', 'related_keywords' => [], 'reader_questions' => [], 'missing_information' => []])]]]]]])
            ->push(['candidates' => [['content' => ['parts' => [['text' => json_encode($this->articleResult())]]]]]]);
        $pipeline = app(ArticlePipeline::class);
        $pipeline->advance($generation);
        $this->assertSame('writing', $generation->fresh()->stage);
        $this->assertNotNull($generation->fresh()->research);
        $pipeline->advance($generation);
        $this->assertSame('review', $generation->fresh()->stage);
        $this->assertNotNull($generation->fresh()->result);
        $pipeline->advance($generation);
        $this->assertSame('completed', $generation->fresh()->status);
        $this->assertTrue($generation->fresh()->review['requires_editor']);
        $this->assertDatabaseHas('posts', ['status' => 'draft', 'ai_reviewed' => 0]);
        $this->assertDatabaseMissing('posts', ['status' => 'published']);
        Http::assertSentCount(2);
        $this->assertSame(['gemini-3.7-flash'], config('services.gemini.fallback_models'));
    }

    public function test_buoc_nghien_cuu_dat_muc_suy_nghi_thap_cho_nhanh(): void
    {
        $generation = $this->generation();
        Http::fake(['*' => Http::response(['error' => ['message' => 'Overloaded']], 503)]);

        (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions()->handle(app(ArticlePipeline::class));

        Http::assertSent(fn ($request) => data_get($request->data(), 'generationConfig.thinkingConfig.thinkingLevel') === 'low');
    }

    public function test_co_the_tat_muc_suy_nghi_thap_qua_cau_hinh(): void
    {
        config(['services.gemini.research_thinking' => '']);
        $generation = $this->generation();
        Http::fake(['*' => Http::response(['error' => ['message' => 'Overloaded']], 503)]);

        (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions()->handle(app(ArticlePipeline::class));

        Http::assertSent(fn ($request) => ! isset($request->data()['generationConfig']['thinkingConfig']));
    }

    public function test_timeout_retries_with_backoff_and_rotates_model_then_stops(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28: timeout test-key'));
        $generation = $this->generation();
        foreach ([30, 120, 300, null] as $delay) {
            $job = (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions();
            $job->handle(app(ArticlePipeline::class));
            if ($delay !== null) {
                $job->assertReleased($delay);
            } else {
                $job->assertNotReleased();
            }
        }
        $generation->refresh();
        $this->assertSame('failed', $generation->status);
        $this->assertSame(4, $generation->failures);
        $this->assertStringNotContainsString('test-key', $generation->last_error);
    }

    public function test_overload_uses_fallback_on_next_attempt(): void
    {
        Http::fake(['*' => Http::response(['error' => ['message' => 'Overloaded']], 503)]);
        $generation = $this->generation();
        for ($i = 0; $i < 2; $i++) {
            (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions()->handle(app(ArticlePipeline::class));
        }
        $urls = collect(Http::recorded())->map(fn ($pair) => $pair[0]->url())->all();
        $this->assertStringContainsString('gemini-3.8-flash', $urls[0]);
        $this->assertStringContainsString('gemini-3.7-flash', $urls[1]);
        $this->assertSame('retrying', $generation->fresh()->status);
    }

    public function test_auth_error_stops_without_retries(): void
    {
        Http::fake(['*' => Http::response([], 403)]);
        $generation = $this->generation();
        $job = (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions();
        $job->handle(app(ArticlePipeline::class));
        $job->assertNotReleased();
        $this->assertSame('failed', $generation->fresh()->status);
    }

    public function test_manual_retry_preserves_checkpoint_and_old_job_cannot_run(): void
    {
        Http::fake();
        $generation = $this->generation(['status' => 'failed', 'stage' => 'writing', 'research' => ['brief' => ['outline' => ['A', 'B', 'C']]]]);
        app(GenerationRequests::class)->retry($generation->id, $this->editor->id);
        $generation->refresh();
        $this->assertSame(2, $generation->run);
        $this->assertSame('writing', $generation->stage);
        $this->assertNotNull($generation->research);
        (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions()->handle(app(ArticlePipeline::class));
        Http::assertNothingSent();
        Queue::assertPushed(GenerateArticle::class, 1);
    }

    public function test_completed_job_is_idempotent_and_worker_failure_is_visible(): void
    {
        Http::fake();
        $generation = $this->generation(['status' => 'completed']);
        $job = (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions();
        $job->handle(app(ArticlePipeline::class));
        $job->failed(null);
        $this->assertSame('completed', $generation->fresh()->status);
        $generation->update(['status' => 'processing']);
        $job->failed(null);
        $this->assertSame('failed', $generation->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_review_flags_missing_sources_links_and_duplicate_content(): void
    {
        $result = $this->articleResult();
        $result['article_html'] .= '<a href="https://example.com/invented">Nguồn</a><p>Giá 500 triệu.</p>';
        Post::create(['title' => $this->input()['title'], 'slug' => 'existing', 'status' => 'published', 'sections' => [['type' => 'text', 'body' => $result['article_html']]]]);
        $generation = $this->generation(['result' => $result, 'research' => ['grounded' => false]]);
        $review = app(ArticleReview::class)->check($generation);
        $this->assertFalse($review['checks']['Có nguồn tìm kiếm trực tiếp']);
        $this->assertNotEmpty($review['similar_articles']);
        $this->assertStringContainsString('Liên kết chưa có', implode(' ', $review['warnings']));
        $this->assertStringContainsString('đối chiếu nguồn', implode(' ', $review['warnings']));
    }

    public function test_review_canh_bao_the_title_viet_hoa_tung_chu(): void
    {
        $result = $this->articleResult();
        $result['seo_title'] = 'VinFast Limo Green Đồng Hành Cùng Hành Trình Chuyển Đổi Xanh';
        $generation = $this->generation(['result' => $result, 'research' => ['brief' => ['keyword' => 'limo green']]]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertFalse($review['checks']['Thẻ title viết hoa theo lối tiếng Việt']);
    }

    public function test_review_khong_canh_bao_khi_the_title_viet_hoa_dung(): void
    {
        $result = $this->articleResult();
        $result['seo_title'] = 'VinFast VF 3 cho gia đình nhỏ tại Bắc Giang';
        $generation = $this->generation(['result' => $result, 'research' => ['brief' => ['keyword' => 'vf 3']]]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertTrue($review['checks']['Thẻ title viết hoa theo lối tiếng Việt']);
    }

    public function test_tu_khoa_chinh_khong_can_trung_nguyen_cum_trong_title(): void
    {
        $result = $this->articleResult();
        $result['seo_title'] = 'VinFast VF 5 Plus có phù hợp chạy dịch vụ ở Bắc Giang không?';
        $generation = $this->generation([
            'result' => $result,
            'research' => ['brief' => ['keyword' => 'VF 5 Plus chạy dịch vụ Bắc Giang']],
        ]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertTrue($review['checks']['Từ khóa chính trong tiêu đề SEO']);
    }

    public function test_title_lac_de_van_bi_canh_bao_tu_khoa(): void
    {
        $result = $this->articleResult();
        $result['seo_title'] = 'Bảng giá phụ kiện tháng 11';
        $generation = $this->generation([
            'result' => $result,
            'research' => ['brief' => ['keyword' => 'VF 5 Plus chạy dịch vụ Bắc Giang']],
        ]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertFalse($review['checks']['Từ khóa chính trong tiêu đề SEO']);
    }

    public function test_review_canh_bao_khi_bai_cai_qua_nhieu_lien_ket(): void
    {
        $products = [];
        $result = $this->articleResult();
        foreach (range(1, 8) as $i) {
            $url = Url::absolute('product', 'xe-'.$i);
            $products[] = ['title' => 'Xe '.$i, 'url' => $url];
            $result['article_html'] .= '<p><a href="'.$url.'">Xe '.$i.'</a></p>';
        }
        $generation = $this->generation([
            'result' => $result,
            'research' => ['brief' => ['keyword' => 'xe điện'], 'products' => $products],
        ]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertStringContainsString('Quá nhiều liên kết', implode(' ', $review['warnings']));
    }

    public function test_review_canh_bao_bai_qua_ngan(): void
    {
        $generation = $this->generation(['result' => $this->articleResult(), 'research' => ['brief' => ['keyword' => 'xe điện']]]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertFalse($review['checks']['Bài đủ dài (từ 1.200 từ)']);
    }

    public function test_lien_ket_sang_trang_xe_khong_bi_coi_la_bia_dat(): void
    {
        $product = catalog_model('product')::create(['name' => 'VinFast VF 6', 'slug' => 'vinfast-vf-6', 'status' => 'published']);
        $url = Url::absolute('product', $product->slug);
        $result = $this->articleResult();
        $result['article_html'] .= '<p>Xem thêm <a href="'.$url.'">VinFast VF 6</a>.</p>';
        $generation = $this->generation([
            'result' => $result,
            'research' => ['brief' => ['keyword' => 'xe điện'], 'products' => [['title' => 'VinFast VF 6', 'url' => $url]]],
        ]);

        $review = app(ArticleReview::class)->check($generation);

        $this->assertStringNotContainsString('Liên kết chưa có', implode(' ', $review['warnings']));
    }

    public function test_buoc_viet_duoc_cung_cap_duong_dan_trang_xe(): void
    {
        catalog_model('product')::create(['name' => 'VinFast VF 6', 'slug' => 'vinfast-vf-6', 'status' => 'published']);
        $generation = $this->generation();
        Http::fake(['*' => Http::response(['error' => ['message' => 'Overloaded']], 503)]);

        (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions()->handle(app(ArticlePipeline::class));

        Http::assertSent(fn ($request) => str_contains(json_encode($request->data(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '/san-pham/vinfast-vf-6'));
    }

    public function test_publication_requires_editor_confirmation(): void
    {
        $generation = $this->generation(['status' => 'completed']);
        PublicationReview::validate(['ai_generation_id' => $generation->id, 'status' => 'draft']);
        $this->expectException(ValidationException::class);
        PublicationReview::validate(['ai_generation_id' => $generation->id, 'status' => 'published', 'ai_reviewed' => false]);
    }

    public function test_search_sources_are_from_grounding_metadata_not_model_claims(): void
    {
        $input = $this->input() + ['use_search' => true];
        $generation = $this->generation(['input' => $input]);
        $brief = ['keyword' => 'xe điện', 'intent' => 'information', 'intent_analysis' => 'Tìm hiểu xe điện', 'outline' => ['A', 'B', 'C'], 'related_keywords' => ['VinFast'], 'reader_questions' => [], 'evidence_notes' => 'Cần đối chiếu.', 'missing_information' => [], 'sources' => ['https://invented.example']];
        Http::fake(['*' => Http::response(['candidates' => [[
            'content' => ['parts' => [['text' => json_encode($brief)]]],
            'groundingMetadata' => [
                'groundingChunks' => [['web' => ['uri' => 'https://vinfastauto.com/vn_vi', 'title' => 'VinFast']], ['web' => ['uri' => 'javascript:alert(1)', 'title' => 'Unsafe']]],
                'searchEntryPoint' => ['renderedContent' => '<div>Google Search</div>'],
            ],
        ]]])]);
        app(ArticlePipeline::class)->advance($generation);
        $research = $generation->fresh()->research;
        $this->assertTrue($research['grounded']);
        $this->assertSame([['url' => 'https://vinfastauto.com/vn_vi', 'title' => 'VinFast']], $research['sources']);
        $this->assertArrayNotHasKey('sources', $research['brief']);
        Http::assertSent(fn ($request) => isset($request->data()['tools'][0]['googleSearch']));
    }

    public function test_failed_writing_keeps_research_and_restores_configuration(): void
    {
        $research = ['brief' => ['outline' => ['A', 'B', 'C']], 'grounded' => false];
        $generation = $this->generation(['stage' => 'writing', 'research' => $research]);
        $response = [];
        data_set($response, 'candidates.0.content.parts.0.text', '{broken');
        Http::fake(['*' => Http::response($response)]);
        $original = config('services.gemini');
        (new GenerateArticle($generation->id, 1))->withFakeQueueInteractions()->handle(app(ArticlePipeline::class));
        $generation->refresh();
        $this->assertSame('writing', $generation->stage);
        $this->assertSame('retrying', $generation->status);
        $this->assertSame($research, $generation->research);
        $this->assertNull($generation->result);
        $this->assertSame($original, config('services.gemini'));
    }

    public function test_another_editor_cannot_retry_request(): void
    {
        $generation = $this->generation(['status' => 'failed']);
        $other = User::create(['name' => 'Other', 'email' => 'retry-other@test.local', 'password' => 'x']);
        $this->expectException(ModelNotFoundException::class);
        app(GenerationRequests::class)->retry($generation->id, $other->id);
    }

    public function test_editor_can_save_ai_draft_but_must_confirm_before_publishing(): void
    {
        $generation = $this->generation(['status' => 'completed', 'result' => $this->articleResult(), 'review' => ['warnings' => [], 'note' => 'Đọc duyệt']]);
        $post = app(ArticleDraft::class)->apply($generation);
        $component = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()]);

        $component->fillForm(['status' => 'published'])->call('save')->assertHasErrors(['data.ai_reviewed']);
        $this->assertSame('draft', $post->fresh()->status);

        $component->fillForm(['status' => 'published', 'ai_reviewed' => true])->call('save')->assertHasNoErrors();
        $this->assertSame('published', $post->fresh()->status);
    }
}
