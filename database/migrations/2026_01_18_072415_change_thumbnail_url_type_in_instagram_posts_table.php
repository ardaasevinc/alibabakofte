<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('instagram_posts', function (Blueprint $table) {
        // change() yerine direkt tipi yazıyoruz çünkü sütun henüz yok
        $table->text('thumbnail_url')->nullable()->after('media_url');
    });
}

public function down(): void
{
    Schema::table('instagram_posts', function (Blueprint $table) {
        $table->dropColumn('thumbnail_url');
    });
}
};
