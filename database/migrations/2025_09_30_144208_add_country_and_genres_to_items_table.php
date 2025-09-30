<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('items', function (Blueprint $t) {
      $t->string('country')->nullable()->after('end_date');
      $t->string('genre_names')->nullable()->after('country'); // "スリラー, ホラー" などのカンマ区切り
    });
  }
  public function down(): void {
    Schema::table('items', function (Blueprint $t) {
      $t->dropColumn(['country','genre_names']);
    });
  }
};
