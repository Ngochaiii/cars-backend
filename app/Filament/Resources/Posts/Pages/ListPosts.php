<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Services\Articles\GenerationRequests;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('writeArticleWithAi')
                ->label('Viết bài bằng AI')
                ->icon(Heroicon::OutlinedSparkles)
                ->modalHeading('Viết bài bằng AI')
                ->modalDescription('Chỉ cần tiêu đề. Gemini tự nghiên cứu từ khóa, viết bài chuẩn SEO/GEO rồi tạo sẵn bài ở trạng thái Nháp để bạn đọc và bấm đăng.')
                ->modalSubmitActionLabel('Bắt đầu viết')
                ->schema([
                    TextInput::make('title')
                        ->label('Tiêu đề bài viết')
                        ->placeholder('Ví dụ: Đánh giá VinFast VF 3 cho gia đình nhỏ ở Bắc Giang')
                        ->required()
                        ->maxLength(255)
                        ->autofocus(),
                ])
                ->action(function (array $data, GenerationRequests $requests): void {
                    $requests->start(auth()->id(), ['title' => $data['title']]);

                    Notification::make()
                        ->title('Đang viết bài')
                        ->body('Bạn có thể đóng trang. Bài nháp sẽ tự xuất hiện trong danh sách khi Gemini viết xong.')
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
