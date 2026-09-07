<?php

namespace Tests\Feature;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Media\MediaStore;
use App\Models\User;
use App\Services\GeminiArticleWriter;
use App\Support\RichText;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class GeminiArticleWriterTest extends TestCase
{
    private string $imagePath = 'catalog/posts/gemini-cover.jpg';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.key' => 'gemini-test-key',
            'services.gemini.model' => 'gemini-3.8-flash',
            'services.gemini.fallback_models' => ['gemini-3.7-flash'],
            'services.gemini.google_search' => true,
        ]);

        app(MediaStore::class)->write(
            $this->imagePath,
            UploadedFile::fake()->image('gemini-cover.jpg', 1200, 675)->getContent(),
        );
    }

    public function test_gui_tieu_de_anh_va_nhan_json_seo_tu_gemini(): void
    {
        $articleHtml = '<p>'.str_repeat('Nội dung hữu ích về xe điện VinFast Bắc Giang. ', 20).'</p>';

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'excerpt' => 'Tóm tắt bài viết hấp dẫn.',
                                'article_html' => $articleHtml,
                                'seo_title' => 'VinFast VF 3 2026 tại Bắc Giang',
                                'meta_description' => 'Khám phá VinFast VF 3, chi phí sử dụng và kinh nghiệm lựa chọn xe điện phù hợp cho khách hàng tại Bắc Giang.',
                                'keywords' => [
                                    'VinFast VF 3',
                                    'xe điện VinFast',
                                    'VinFast Bắc Giang',
                                    'giá xe VinFast',
                                    'kinh nghiệm mua xe điện',
                                ],
                            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        ]],
                    ],
                ]],
            ]),
        ]);

        $result = app(GeminiArticleWriter::class)->generate(
            'VinFast VF 3 có phù hợp đi phố?',
            $this->imagePath,
            'Viết cho người mua xe lần đầu.',
        );

        $this->assertSame('VinFast VF 3 2026 tại Bắc Giang', $result['seo_title']);
        $this->assertCount(5, $result['keywords']);
        $this->assertStringContainsString('xe điện VinFast', $result['article_html']);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent'
                && $request->hasHeader('x-goog-api-key', 'gemini-test-key')
                && data_get($data, 'contents.0.parts.0.inlineData.mimeType') === 'image/jpeg'
                && filled(data_get($data, 'contents.0.parts.0.inlineData.data'))
                && array_key_exists('googleSearch', data_get($data, 'tools.0', []))
                && data_get($data, 'generationConfig.maxOutputTokens') === 4500
                && data_get($data, 'generationConfig.responseFormat.text.mimeType') === 'APPLICATION_JSON';
        });
    }

    public function test_khong_co_api_key_thi_bao_loi_ro_rang_va_khong_goi_mang(): void
    {
        config(['services.gemini.key' => null]);
        Http::fake();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('GEMINI_API_KEY');

        try {
            app(GeminiArticleWriter::class)->generate('Một tiêu đề', $this->imagePath);
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_free_tier_khong_gui_google_search_nhung_van_yeu_cau_tu_khoa_seo(): void
    {
        config(['services.gemini.google_search' => false]);
        $articleHtml = '<p>'.str_repeat('Nội dung hữu ích về xe điện VinFast. ', 20).'</p>';

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'excerpt' => 'Tóm tắt bài viết.',
                            'article_html' => $articleHtml,
                            'seo_title' => 'Tiêu đề SEO VinFast',
                            'meta_description' => 'Nội dung mô tả SEO hữu ích về xe điện VinFast dành cho khách hàng đang tìm hiểu và lựa chọn xe phù hợp.',
                            'keywords' => ['xe điện', 'VinFast', 'VinFast Bắc Giang'],
                        ], JSON_UNESCAPED_UNICODE),
                    ]]],
                ]],
            ]),
        ]);

        app(GeminiArticleWriter::class)->generate('Một tiêu đề', $this->imagePath);

        Http::assertSent(function (Request $request): bool {
            $data = $request->data();

            return ! array_key_exists('tools', $data)
                && str_contains((string) data_get($data, 'contents.0.parts.1.text'), 'cụm từ khóa sát chủ đề');
        });
    }

    public function test_loi_quota_tu_gemini_duoc_doi_thanh_thong_bao_de_hieu_va_khong_goi_lap(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => ['message' => 'Quota exceeded'],
            ], 429),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('hết hạn mức tức thời');

        try {
            app(GeminiArticleWriter::class)->generate('Một tiêu đề', $this->imagePath);
        } finally {
            Http::assertSentCount(1);
        }
    }

    public function test_loi_qua_tai_tu_dong_chuyen_sang_model_du_phong(): void
    {
        $articleHtml = '<p>'.str_repeat('Nội dung bài viết sau khi Gemini hết quá tải. ', 20).'</p>';

        Http::fakeSequence()
            ->push(['error' => ['message' => 'High demand']], 503)
            ->push([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'excerpt' => 'Tóm tắt sau retry.',
                            'article_html' => $articleHtml,
                            'seo_title' => 'SEO title sau retry',
                            'meta_description' => 'Meta description hợp lệ sau khi hệ thống tự thử lại request Gemini bị quá tải và nhận đủ dữ liệu thành công.',
                            'keywords' => ['xe điện', 'VinFast', 'kinh nghiệm mua xe'],
                        ], JSON_UNESCAPED_UNICODE),
                    ]]],
                ]],
            ], 200);

        $result = app(GeminiArticleWriter::class)->generate('Một tiêu đề', $this->imagePath);

        $this->assertSame('SEO title sau retry', $result['seo_title']);
        Http::assertSentCount(2);

        $urls = collect(Http::recorded())
            ->map(fn (array $pair): string => $pair[0]->url())
            ->all();

        $this->assertSame([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.7-flash:generateContent',
        ], $urls);
    }

    public function test_nut_gemini_luon_bam_duoc_va_bao_thieu_du_lieu(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'gemini-missing-data@test.local',
            'password' => 'x',
        ]));
        Http::fake();

        $action = TestAction::make('generateArticleWithGemini')
            ->schemaComponent('geminiArticleActions');

        Livewire::test(CreatePost::class)
            ->assertActionEnabled($action)
            ->callAction($action)
            ->assertNotified('Chưa đủ thông tin');

        Http::assertNothingSent();
    }

    public function test_nut_gemini_do_noi_dung_vao_form_nhung_chua_tu_dong_dang_bai(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'gemini-admin@test.local',
            'password' => 'x',
        ]));

        $articleHtml = '<p>'.str_repeat('Thông tin hữu ích cho người đang tìm hiểu xe điện. ', 20).'</p>';

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'excerpt' => 'Tóm tắt do Gemini tạo.',
                            'article_html' => $articleHtml,
                            'seo_title' => 'SEO title do Gemini tạo',
                            'meta_description' => 'Meta description do Gemini tạo cho bài viết xe điện VinFast tại đại lý Bắc Giang, cung cấp thông tin hữu ích cho khách hàng.',
                            'keywords' => ['xe điện', 'VinFast', 'VinFast Bắc Giang', 'mua xe điện', 'kinh nghiệm chọn xe'],
                        ], JSON_UNESCAPED_UNICODE),
                    ]]],
                ]],
            ]),
        ]);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Kinh nghiệm chọn xe điện',
                'slug' => 'kinh-nghiem-chon-xe-dien',
                'cover' => [$this->imagePath],
                'status' => 'draft',
            ])
            ->callAction(
                TestAction::make('generateArticleWithGemini')
                    ->schemaComponent('geminiArticleActions'),
            )
            ->assertFormSet([
                'excerpt' => 'Tóm tắt do Gemini tạo.',
                'article_body' => str_replace(' </p>', '</p>', RichText::clean($articleHtml)),
                'seo.title' => 'SEO title do Gemini tạo',
                'seo.keywords' => 'xe điện, VinFast, VinFast Bắc Giang, mua xe điện, kinh nghiệm chọn xe',
                'status' => 'draft',
            ]);
    }
}
