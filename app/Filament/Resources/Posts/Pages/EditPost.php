<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Services\Articles\GenerationRequests;
use App\Services\Articles\PublicationReview;
use App\Support\PostContent;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Đăng bài trong một lần bấm: xác nhận là chỗ biên tập viên nhận
            // trách nhiệm đã đọc nội dung AI, thay cho ô tích riêng trước đây.
            Action::make('publishArticle')
                ->label('Đăng bài')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (): bool => $this->getRecord()->status !== 'published')
                ->requiresConfirmation()
                ->modalHeading('Đăng bài này lên website?')
                ->modalDescription('Tôi đã đọc nội dung, đối chiếu số liệu và nguồn. Bài sẽ hiển thị công khai ngay.')
                ->modalSubmitActionLabel('Đăng ngay')
                ->action(function (): void {
                    $post = $this->getRecord();
                    $post->update([
                        'status' => 'published',
                        'published_at' => $post->published_at ?? now(),
                        'ai_reviewed' => true,
                    ]);
                    $this->fillForm();

                    Notification::make()->title('Bài đã được đăng')->success()->send();
                }),

            Action::make('rewriteArticleWithAi')
                ->label('Viết lại bằng AI')
                ->icon(Heroicon::OutlinedSparkles)
                ->requiresConfirmation()
                ->modalHeading('Viết lại bài này bằng AI?')
                ->modalDescription('Gemini sẽ nghiên cứu lại theo tiêu đề hiện tại và thay nội dung, tóm tắt, hỏi đáp cùng các thẻ SEO của bài này.')
                ->modalSubmitActionLabel('Viết lại')
                ->action(function (GenerationRequests $requests): void {
                    $post = $this->getRecord();
                    $requests->start(auth()->id(), ['title' => $post->title, 'post_id' => $post->getKey()]);

                    Notification::make()
                        ->title('Đang viết lại bài')
                        ->body('Bạn có thể đóng trang. Nội dung mới sẽ được điền vào bài này khi Gemini viết xong.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['article_body'] = PostContent::fromSections($data['sections'] ?? []);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        PublicationReview::validate($data);

        $data['sections'] = PostContent::intoSections(
            $data['article_body'] ?? null,
            $this->getRecord()->sections ?? [],
        );
        unset($data['article_body']);

        return $data;
    }
}
