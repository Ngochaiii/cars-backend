<?php

namespace App\Jobs;

use App\Models\ArticleGeneration;
use App\Services\Articles\ArticlePipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateArticle implements ShouldQueue
{
    use Queueable;

    public int $tries = 20;

    public int $timeout = 110;

    public bool $failOnTimeout = true;

    public function __construct(public int $generationId, public int $run)
    {
        $this->onConnection('article_generation')->onQueue('articles');
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('article-'.$this->generationId))->releaseAfter(15)->expireAfter(140)];
    }

    public function handle(ArticlePipeline $pipeline): void
    {
        $generation = ArticleGeneration::find($this->generationId);
        if (! $generation || $generation->run !== $this->run || in_array($generation->status, ['completed', 'failed'], true)) {
            return;
        }
        $generation->update(['status' => 'processing', 'next_attempt_at' => null]);
        try {
            $pipeline->advance($generation);
            if ($generation->fresh()->status !== 'completed') {
                $this->release(1);
            }
        } catch (Throwable $exception) {
            $failures = $generation->failures + 1;
            $terminal = $failures >= 4 || in_array((int) $exception->getCode(), [400, 401, 403], true);
            $delay = [30, 120, 300][$failures - 1] ?? 300;
            // API keys and raw request payloads must never be persisted in user-facing errors.
            $message = str_replace((string) config('services.gemini.key'), '[redacted]', $exception->getMessage());
            if ($exception instanceof ConnectionException) {
                $message = 'Gemini chưa phản hồi trong thời gian chờ hoặc kết nối mạng bị gián đoạn. Hệ thống giữ nguyên tiến độ để thử lại.';
            }
            $generation->update([
                'status' => $terminal ? 'failed' : 'retrying',
                'failures' => $failures,
                'last_error' => mb_substr($message, 0, 800),
                'next_attempt_at' => $terminal ? null : now()->addSeconds($delay),
            ]);
            Log::warning('Article generation attempt failed', ['id' => $generation->id, 'stage' => $generation->stage, 'attempt' => $failures]);
            if (! $terminal) {
                $this->release($delay);
            }
        }
    }

    public function failed(?Throwable $exception): void
    {
        ArticleGeneration::whereKey($this->generationId)->where('run', $this->run)
            ->whereNotIn('status', ['completed', 'failed'])->update([
                'status' => 'failed', 'next_attempt_at' => null,
                'last_error' => 'Tiến trình bị gián đoạn hoặc vượt thời gian cho phép. Bạn có thể thử lại từ bước đã lưu.',
            ]);
    }
}
