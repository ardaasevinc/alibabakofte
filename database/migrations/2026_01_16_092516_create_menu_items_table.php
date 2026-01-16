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
    Schema::create('menu_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('menu_category_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->text('desc')->nullable();
        $table->decimal('price', 10, 2)->nullable(); // Fiyat alanı
        $table->string('image')->nullable();
        $table->integer('order')->default(0);
        $table->boolean('is_published')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
