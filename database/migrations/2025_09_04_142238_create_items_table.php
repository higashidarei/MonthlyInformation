<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $t) {
            $t->id();
            $t->enum('type', ['movie', 'book', 'exhibition']);
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('image_url')->nullable();
            $t->string('detail_url')->nullable();
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();

            // 固有フィールド（どれかが使われる）
            $t->string('director')->nullable(); // 映画
            $t->string('author')->nullable();   // 本
            $t->string('venue')->nullable();    // 展覧会

            // 取得メタ
            $t->string('source');      // tmdb|google_books|scrape
            $t->string('source_id');   // 外部ID
            $t->string('month_tag');   // 例: 2025-09

            $t->timestamps();
            $t->unique(['source', 'source_id', 'month_tag']); // 重複取り込み防止
            $t->index(['type', 'month_tag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
