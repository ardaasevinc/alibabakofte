<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('leads', function (Blueprint $table) {
        $table->id();

        // Lead Kaynağı
        $table->string('type')->index();            // whatsapp, menu, vb.
        $table->string('event_id')->unique();       // deduplication
        $table->string('event_name')->nullable();   // Lead, ViewContent vb.

        // Traffic Source
        $table->string('utm_source')->nullable();
        $table->string('utm_campaign')->nullable();
        $table->string('utm_medium')->nullable();
        $table->string('fbclid')->nullable();
        $table->string('gclid')->nullable();

        // Device & Session
        $table->string('device_id')->nullable();           // hashed device id
        $table->string('session_hash')->nullable();        // hashed session id
        $table->string('fbp')->nullable();                 // meta browser id
        $table->string('fbc')->nullable();
        $table->string('browser_id')->nullable();          // client fingerprint

        // Technical Client Info
        $table->string('ip_address')->nullable();
        $table->text('user_agent')->nullable();
        $table->string('referer')->nullable();
        $table->string('landing_page')->nullable();

        // Extra data
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
