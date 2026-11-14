<?php

namespace App\Filament\Schemas;

use App\Models\ArticleGeneration;
use App\Services\Articles\GenerationRequests;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Illuminate\Support\Str;

class ArticleGenerationSection
{
    public static function selected(Get $get): ?ArticleGeneration
    {
        return ArticleGeneration::where('user_id', auth()->id())->find($get('generation_request_id'));
    }

    public static function make(): Section
    {
        return Section::make('Trợ lý viết bài SEO / GEO')
            ->description('Tạo bản nháp có nghiên cứu, dàn ý và kiểm tra nội dung. Yêu cầu được lưu để tiếp tục khi bạn quay lại. Không tự đăng bài.')
            ->columnSpanFull()->schema([
                TextInput::make('ai_keyword')->label('Từ khóa chính')->maxLength(200)->dehydrated(false),
                TextInput::make('ai_related_keywords')->label('Từ khóa liên quan')->maxLength(1000)->dehydrated(false),
                Select::make('ai_intent')->label('Mục đích tìm kiếm')->options(['information' => 'Tìm hiểu thông tin', 'comparison' => 'So sánh, lựa chọn', 'purchase' => 'Giá, tư vấn mua xe'])->default('information')->dehydrated(false),
                TextInput::make('ai_location')->label('Khu vực khách hàng')->default('Bắc Giang')->maxLength(200)->dehydrated(false),
                Checkbox::make('ai_use_search')->label('Tra cứu nguồn bằng Google Search')
                    ->default(fn () => (bool) config('services.gemini.google_search'))
                    ->helperText('Cần quyền sử dụng Google Search trong dự án Gemini; có thể phát sinh phí theo gói API. Nếu tắt, bài dựa vào dữ kiện bạn nhập và phải kiểm chứng trước khi đăng.')
                    ->dehydrated(false),
                Textarea::make('ai_facts')->label('Dữ kiện và nguồn đã xác nhận')->helperText('Nhập thông tin đại lý, giá/ưu đãi đã duyệt, ngày hiệu lực và URL nguồn chính thức. AI không được tự bịa dữ kiện còn thiếu.')->rows(4)->maxLength(12000)->dehydrated(false),
                Textarea::make('ai_instructions')->label('Yêu cầu thêm cho Gemini')->rows(2)->maxLength(4000)->dehydrated(false),
                Actions::make([
                    Action::make('generateArticleWithGemini')->label('Sinh bài viết bằng Gemini')->icon('heroicon-o-sparkles')
                        ->action(function (Get $schemaGet, Set $schemaSet, GenerationRequests $requests): void {
                            $cover = $schemaGet('cover');
                            $cover = is_array($cover) ? collect($cover)->first(fn ($item) => filled($item)) : $cover;
                            if (blank($schemaGet('title')) || blank($cover) || blank($schemaGet('ai_keyword'))) {
                                Notification::make()->title('Chưa đủ thông tin')->body('Hãy bổ sung tiêu đề, ảnh bìa đã tải xong và từ khóa chính.')->warning()->send();

                                return;
                            }
                            $generation = $requests->start(auth()->id(), [
                                'title' => $schemaGet('title'), 'cover' => $cover,
                                'keyword' => $schemaGet('ai_keyword'), 'related_keywords' => $schemaGet('ai_related_keywords'),
                                'use_search' => (bool) $schemaGet('ai_use_search'),
                                'intent' => $schemaGet('ai_intent') ?: 'information', 'location' => $schemaGet('ai_location'),
                                'facts' => $schemaGet('ai_facts'), 'instructions' => $schemaGet('ai_instructions'),
                            ]);
                            $schemaSet('generation_request_id', $generation->id);
                            Notification::make()->title('Yêu cầu đã được lưu')->body('Bạn có thể đóng trang. Chọn yêu cầu trong lịch sử để xem tiến độ và nhận bản nháp.')->success()->send();
                        }),
                ])->key('geminiArticleActions'),
                Select::make('generation_request_id')->label('Yêu cầu đã lưu của bạn')->dehydrated(false)->live()->searchable()
                    ->options(fn () => ArticleGeneration::where('user_id', auth()->id())->latest('id')->limit(100)->get()->mapWithKeys(fn ($item) => [$item->id => '#'.$item->id.' · '.$item->input['title'].' · '.$item->statusLabel()])->all()),
                View::make('filament.articles.generation-status')->viewData(fn (Get $get) => ['generation' => self::selected($get)]),
                Actions::make([
                    Action::make('loadGeneratedArticle')->label('Đưa bản nháp vào trình soạn thảo')
                        ->visible(fn (Get $get) => self::selected($get)?->status === 'completed')
                        ->requiresConfirmation()->modalDescription('Thay tiêu đề, ảnh, nội dung và SEO trong trình soạn thảo bằng bản nháp đã chọn. Bài sẽ ở trạng thái Nháp cho đến khi bạn đọc duyệt và lưu.')
                        ->action(function (Get $schemaGet, Set $schemaSet): void {
                            $generation = self::selected($schemaGet);
                            abort_unless($generation?->status === 'completed', 403);
                            $article = $generation->result;
                            foreach (['title' => $generation->input['title'], 'slug' => Str::slug($generation->input['title']), 'cover' => $generation->input['cover'],
                                'excerpt' => $article['excerpt'], 'article_body' => $article['article_html'],
                                'seo.title' => $article['seo_title'], 'seo.description' => $article['meta_description'], 'seo.keywords' => implode(', ', $article['keywords']),
                                'status' => 'draft', 'ai_generation_id' => $generation->id, 'ai_reviewed' => false] as $field => $value) {
                                $schemaSet($field, $value);
                            }
                            Notification::make()->title('Đã đưa vào trình soạn thảo')->body('Đọc lại nội dung, đối chiếu nguồn rồi lưu bản nháp.')->success()->send();
                        }),
                    Action::make('retryGeneratedArticle')->label('Thử lại từ bước đã lưu')
                        ->visible(fn (Get $get) => self::selected($get)?->status === 'failed')
                        ->action(function (Get $schemaGet, GenerationRequests $requests): void {
                            $generation = self::selected($schemaGet);
                            abort_unless($generation, 403);
                            $requests->retry($generation->id, auth()->id());
                            Notification::make()->title('Đã xếp lịch thử lại')->success()->send();
                        }),
                ])->key('generationResultActions'),
            ]);
    }
}
