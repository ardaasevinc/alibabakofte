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
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        // İletişim ve Adres
        $table->text('work_time')->nullable();
        $table->text('address')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->string('slogan')->nullable();

        // Görseller
        $table->string('logo_light')->nullable();
        $table->string('logo_dark')->nullable();
        $table->string('favicon')->nullable();

        // Harita ve Sosyal
        $table->text('map_iframe')->nullable();
        $table->text('map_link')->nullable();
        $table->text('gpage_link')->nullable();
        $table->text('facebook_url')->nullable();
        $table->text('instagram_url')->nullable();

        // SEO & Analytics
        $table->string('meta_title')->nullable();
        $table->text('meta_desc')->nullable();
        $table->text('meta_keywords')->nullable();
        $table->text('google_analytics_code')->nullable();
        $table->text('facebook_pixel_code')->nullable();

        // ENV Bazlı Alanlar
        $table->string('app_url')->nullable();
        $table->string('app_env')->nullable();
        $table->boolean('app_debug')->default(false);
        $table->string('instagram_access_token')->nullable();
        $table->string('instagram_app_secret')->nullable();

        // Mail Ayarları
        $table->string('mail_mailer')->default('smtp');
        $table->string('mail_host')->nullable();
        $table->string('mail_port')->nullable();
        $table->string('mail_username')->nullable();
        $table->string('mail_password')->nullable();
        $table->string('mail_from_address')->nullable();
        $table->string('mail_from_name')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
