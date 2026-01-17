<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Schedule eklendi

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

// Mevcut ilham verici söz komutu
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
|--------------------------------------------------------------------------
| Otomatik Görev Planlayıcı (Schedule)
|--------------------------------------------------------------------------
*/

// Instagram Token Yenileme: Her ayın 1'inde gece 03:00'te çalışır
Schedule::command('instagram:refresh-token')
    ->monthlyOn(1, '03:00')
    ->onOneServer()
    ->runInBackground();

// Sitemap Oluşturma: Her gece 00:00'da çalışır
Schedule::command('sitemap:generate')
    ->daily()
    ->at('00:00')
    ->onOneServer()
    ->runInBackground();

// Uygulama Önbelleğini Haftalık Temizleme (Opsiyonel - Sunucu sağlığı için)
Schedule::command('cache:clear')->weekly();