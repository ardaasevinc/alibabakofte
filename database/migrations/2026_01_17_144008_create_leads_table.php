<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            /** =============================
             *  LEAD ANA BİLGİLERİ
             *  ========================== */
            $table->string('type')->default('whatsapp')->index(); // whatsapp, menu, visit vb.
            $table->string('event_id')->nullable()->index();      // CAPI Deduplication
            $table->string('event_name')->default('Lead');

            /** =============================
             *  UTM PARAMETRELERİ
             *  ========================== */
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_medium')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();

            /** =============================
             *  FACEBOOK TRACKING
             *  ========================== */
            $table->string('fbclid')->nullable();
            $table->string('fbc')->nullable(); // Debug amaçlı
            $table->string('fbp')->nullable(); // Browser ID

            /** =============================
             *  TRAFFIC / KAYNAK
             *  ========================== */
            $table->text('came_from_url')->nullable();

            /** =============================
             *  KULLANICI / CİHAZ BİLGİLERİ
             *  ========================== */
            $table->string('ip_address', 45)->nullable(); // IPv6 destekli
            $table->text('user_agent')->nullable();

            $table->string('platform')->nullable();   // iOS, Android, Desktop
            $table->boolean('is_mobile')->nullable(); // true/false

            /** =============================
             *  EK VERİ
             *  ========================== */
            $table->json('payload')->nullable();

            /** =============================
             *  ZAMAN
             *  ========================== */
            $table->timestamps();

            /** =============================
             *  PERFORMANS İNDEKSLERİ
             *  ========================== */
            $table->index('created_at');      // Tarihe göre raporlama hızlanır
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
