<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('blogs', function (Blueprint $table) {
        $table->id();
        // Kategori ile ilişki (Kategori silinirse bloglar kalsın diye nullOnDelete yaptık)
        $table->foreignId('blog_category_id')->nullable()->constrained()->nullOnDelete();
        
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('desc')->nullable(); // Kısa açıklama (RichEditor)
        $table->string('image')->nullable();
        $table->boolean('is_published')->default(false);
        $table->json('tags')->nullable(); // Etiketler için JSON alan
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
