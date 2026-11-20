<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mỗi dòng là một lượt khách bấm nút gọi hoặc Zalo trên web. Không có
        // thông tin cá nhân — chỉ đủ để biết khách vào từ đâu, xem gì, bấm ở
        // trang nào, và ở khu vực nào (tra từ IP).
        Schema::create('contact_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 10);              // phone | zalo
            $table->string('page')->nullable();         // trang đang xem lúc bấm
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->json('source')->nullable();         // utm_*, gad_campaignid, gclid?, landing, landed_at
            $table->json('trail')->nullable();          // các trang đã xem trước đó [{p, t}]
            $table->string('device', 10)->nullable();   // mobile | desktop
            $table->string('session_hash', 64)->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->string('location')->nullable();     // tra từ IP, như leads.location
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_clicks');
    }
};
