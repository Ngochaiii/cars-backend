<?php

namespace App\Services\Articles;

use App\Jobs\GenerateArticle;
use App\Models\ArticleGeneration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GenerationRequests
{
    public function start(int $userId, array $input): ArticleGeneration
    {
        // Biên tập viên chỉ cần tiêu đề. Từ khóa chính, ý định tìm kiếm và dàn ý
        // do bước nghiên cứu của Gemini tự rút ra; các ô còn lại là tùy chọn.
        $input = Validator::make($input, [
            'title' => 'required|string|max:255',
            'post_id' => 'nullable|integer|exists:posts,id',
            'cover' => 'nullable|string|max:1024',
            'use_search' => 'sometimes|boolean',
            'facts' => 'nullable|string|max:12000',
            'instructions' => 'nullable|string|max:4000',
        ])->validate();

        return DB::transaction(function () use ($userId, $input) {
            User::whereKey($userId)->lockForUpdate()->firstOrFail();
            // One active request per editor prevents repeated clicks and runaway spend.
            $active = ArticleGeneration::where('user_id', $userId)->whereIn('status', ['queued', 'processing', 'retrying'])->first();
            if ($active) {
                return $active;
            }
            $generation = ArticleGeneration::create([
                'user_id' => $userId,
                'post_id' => $input['post_id'] ?? null,
                'input' => $input,
            ]);

            // Tạo luôn bài nháp mang tiêu đề đó để biên tập viên thấy ngay chỗ
            // bài sẽ xuất hiện và theo dõi tiến độ trong đúng bài, thay vì bấm
            // xong không thấy gì. Nội dung do bước cuối của pipeline điền vào.
            $post = $generation->post_id
                ? catalog_model('post')::find($generation->post_id)
                : catalog_model('post')::create([
                    'title' => $input['title'],
                    'status' => 'draft',
                    'ai_generation_id' => $generation->id,
                    'ai_reviewed' => false,
                ]);

            if ($post && ! $generation->post_id) {
                $generation->update(['post_id' => $post->getKey()]);
            }
            GenerateArticle::dispatch($generation->id, $generation->run)->afterCommit();

            return $generation;
        });
    }

    public function retry(int $id, int $userId): ArticleGeneration
    {
        return DB::transaction(function () use ($id, $userId) {
            User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $generation = ArticleGeneration::where('user_id', $userId)->lockForUpdate()->findOrFail($id);
            if ($generation->status !== 'failed') {
                return $generation;
            }
            abort_if(ArticleGeneration::where('user_id', $userId)->whereIn('status', ['queued', 'processing', 'retrying'])->exists(), 409, 'Hãy chờ yêu cầu đang chạy hoàn thành.');
            $generation->update(['status' => 'queued', 'failures' => 0, 'run' => $generation->run + 1, 'last_error' => null, 'next_attempt_at' => null]);
            GenerateArticle::dispatch($generation->id, $generation->run)->afterCommit();

            return $generation;
        });
    }
}
