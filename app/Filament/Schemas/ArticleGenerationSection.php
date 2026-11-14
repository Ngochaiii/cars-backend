<?php

namespace App\Filament\Schemas;

use App\Models\ArticleGeneration;
use App\Services\Articles\GenerationRequests;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;

/**
 * Bảng theo dõi yêu cầu AI của bài đang mở. Không còn ô nhập nào: biên tập
 * viên chỉ nhập tiêu đề ở nút "Viết bài bằng AI", phần còn lại Gemini tự làm
 * rồi tự điền vào bài này.
 */
class ArticleGenerationSection
{
    public static function current(Get $get): ?ArticleGeneration
    {
        return ArticleGeneration::find($get('ai_generation_id'));
    }

    public static function make(): Section
    {
        return Section::make('Trợ lý viết bài SEO / GEO')
            ->description('Tiến độ và kết quả kiểm tra của bản AI đã điền vào bài này. Bài chỉ lên trang khi bạn bấm Đăng bài.')
            ->collapsible()
            ->visible(fn (Get $get): bool => filled($get('ai_generation_id')))
            ->columnSpanFull()
            ->schema([
                View::make('filament.articles.generation-status')
                    ->viewData(fn (Get $get): array => ['generation' => self::current($get)]),

                Actions::make([
                    Action::make('retryGeneratedArticle')
                        ->label('Thử lại từ bước đã lưu')
                        ->visible(fn (Get $get): bool => self::current($get)?->status === 'failed')
                        ->action(function (Get $schemaGet, GenerationRequests $requests): void {
                            $generation = self::current($schemaGet);
                            abort_unless($generation !== null, 404);
                            $requests->retry($generation->id, auth()->id());

                            Notification::make()->title('Đã xếp lịch thử lại')->success()->send();
                        }),
                ])->key('generationResultActions'),
            ]);
    }
}
