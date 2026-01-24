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

            /* ============================================================
             * 1) Kullanıcı / Oturum Bilgileri
             * ============================================================ */
            $table->string('external_id')->nullable()->index();   // benzersiz kullanıcı (cookie)
            $table->string('session_id')->nullable()->index();    // Laravel session ID

            /* ============================================================
             * 2) Meta Deduplication
             * ============================================================ */
            $table->string('event_id')->unique()->index();        // Pixel <-> CAPI eşleşmesi
            $table->string('event_name')->default('Lead')->index();

            /* ============================================================
             * 3) Kaynak (Buton / Event Türü)
             * ============================================================ */
            $table->string('type')->index();                      // whatsapp, menu, rezervasyon vb.
            $table->string('button_id')->nullable()->index();     // meta-whatsapp, meta-menu

            /* ============================================================
             * 4) Trafik Kaynağı / Reklam Verileri
             * ============================================================ */
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable()->index();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();

            $table->string('fbclid')->nullable()->index();        // Facebook click id
            $table->string('gclid')->nullable()->index();         // Google Ads click id

            /* ============================================================
             * 5) Meta Browser IDs
             * ============================================================ */
            $table->string('fbp')->nullable()->index();           // _fbp
            $table->string('fbc')->nullable()->index();           // _fbc

            /* ============================================================
             * 6) Kullanıcı Teknik Bilgileri
             * ============================================================ */
            $table->string('ip_address')->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('platform')->nullable();               // iOS / Android / Desktop
            $table->boolean('is_mobile')->default(false);

            /* ============================================================
             * 7) URL Bilgileri
             * ============================================================ */
            $table->string('came_from_url')->nullable();          // kullanıcının geldiği sayfa
            $table->string('event_source_url')->nullable();       // Meta CAPI'ye gönderilen temiz URL
            $table->string('landing_page')->nullable();           // ilk giriş yaptığı sayfa
            $table->string('referer')->nullable();                // HTTP referer

            /* ============================================================
             * 8) Ekstra JSON Alanları
             * ============================================================ */
            $table->json('payload')->nullable();                  // cihaz bilgileri, dil, saat dilimi vb.

            /* ============================================================
             * 9) Meta Cevap Logları
             * ============================================================ */
            $table->json('meta_request')->nullable();             // Meta'ya gönderilen JSON
            $table->json('meta_response')->nullable();            // Meta'dan dönen JSON

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
