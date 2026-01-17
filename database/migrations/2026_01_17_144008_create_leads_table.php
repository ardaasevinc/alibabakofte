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
    Schema::create('leads', function (Blueprint $table) {
        $table->id();
        $table->string('type')->default('whatsapp')->index();
        $table->string('event_id')->nullable()->index();
        $table->string('event_name')->default('Lead');
        $table->string('utm_source')->nullable()->index();
        $table->string('utm_campaign')->nullable();
        $table->string('utm_medium')->nullable();
        $table->string('fbclid')->nullable();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->json('payload')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
