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
        $input = Validator::make($input, [
            'title' => 'required|string|max:255',
            'cover' => 'required|string|max:1024',
            'keyword' => 'required|string|max:200',
            'related_keywords' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:200',
            'intent' => 'required|in:information,comparison,purchase',
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
            $generation = ArticleGeneration::create(['user_id' => $userId, 'input' => $input]);
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
