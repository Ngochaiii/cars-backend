<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('article_generations', function (Blueprint $table) {
            // Bài nháp do yêu cầu này tạo ra. Có cột này thì lần viết lại sau
            // cập nhật đúng bài cũ thay vì sinh thêm bản trùng.
            $table->foreignId('post_id')->nullable()->after('user_id')->constrained('posts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('article_generations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('post_id');
        });
    }
};
