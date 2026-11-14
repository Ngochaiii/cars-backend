<?php

namespace App\Services\Articles;

use App\Models\ArticleGeneration;
use Illuminate\Validation\ValidationException;

class PublicationReview
{
    public static function validate(array $data): void
    {
        if (empty($data['ai_generation_id'])) {
            return;
        }
        if (! ArticleGeneration::whereKey($data['ai_generation_id'])->where('status', 'completed')->exists()) {
            throw ValidationException::withMessages(['data.ai_reviewed' => 'Yêu cầu AI chưa hoàn thành.']);
        }
        if (($data['status'] ?? 'draft') === 'published' && empty($data['ai_reviewed'])) {
            throw ValidationException::withMessages(['data.ai_reviewed' => 'Hãy đọc duyệt và xác nhận dữ kiện, nguồn trước khi đăng bài AI.']);
        }
    }
}
