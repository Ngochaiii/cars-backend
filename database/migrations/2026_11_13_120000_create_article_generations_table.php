<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('queued')->index();
            $table->string('stage')->default('research');
            $table->unsignedInteger('run')->default(1);
            $table->unsignedInteger('failures')->default(0);
            $table->json('input');
            $table->json('research')->nullable();
            $table->json('result')->nullable();
            $table->json('review')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('ai_generation_id')->nullable()->constrained('article_generations')->nullOnDelete();
            $table->boolean('ai_reviewed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ai_generation_id');
            $table->dropColumn('ai_reviewed');
        });
        Schema::dropIfExists('article_generations');
    }
};
