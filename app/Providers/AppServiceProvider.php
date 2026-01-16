<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Veritabanı bağlantısı ve tablo varlık kontrolü
        // Bu kontrol, deploy sırasında tablolar henüz oluşmamışken artisan komutlarının çökmesini engeller.
        if (!app()->runningInConsole() && Schema::hasTable('settings')) {
            $settings = Setting::first();
            
            // Veriyi tüm view'larla paylaş
            View::share('settings', $settings);
        } else {
            // Eğer konsol çalışıyorsa (migrate vb.) veya tablo yoksa null gönder
            View::share('settings', null);
        }
    }
}