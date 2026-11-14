<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleGeneration extends Model
{
    protected $guarded = [];

    protected $attributes = ['status' => 'queued', 'stage' => 'research', 'run' => 1, 'failures' => 0];

    protected function casts(): array
    {
        return ['run' => 'integer', 'failures' => 'integer', 'input' => 'array', 'research' => 'array', 'result' => 'array', 'review' => 'array', 'next_attempt_at' => 'datetime'];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'queued' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'retrying' => 'Đang chờ thử lại',
            'failed' => 'Tạm dừng — cần thử lại',
            'completed' => 'Đã tạo bản nháp — cần biên tập',
            default => $this->status,
        };
    }

    public function stageLabel(): string
    {
        return match ($this->stage) {
            'research' => 'Nghiên cứu và lập dàn ý',
            'writing' => 'Viết bài',
            'review' => 'Kiểm tra nội dung',
            default => $this->stage,
        };
    }
}
