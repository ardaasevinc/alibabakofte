<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->string('instagram_id')->unique();
            $table->string('media_type'); // IMAGE, VIDEO veya CAROUSEL_ALBUM
            $table->text('media_url');    // Fotoğrafın veya videonun linki
            $table->text('permalink');    // Instagram'daki orijinal gönderi linki
            $table->text('caption')->nullable(); // Fotoğraf altı açıklaması
            $table->boolean('is_published')->default(false); // Varsayılan kapalı (Sen onaylayınca açılır)
            $table->timestamp('posted_at'); // Instagram'da paylaşılma tarihi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_posts');
    }
};