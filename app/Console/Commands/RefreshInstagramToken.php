<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class RefreshInstagramToken extends Command
{
    protected $signature = 'instagram:refresh-token';
    protected $description = 'Instagram Long-Lived Access Token\'ı yeniler.';

    public function handle()
    {
        $setting = Setting::first();

        if ($setting && $setting->instagram_access_token) {
            $response = Http::get('https://graph.instagram.com/refresh_access_token', [
                'grant_type' => 'ig_refresh_token',
                'access_token' => $setting->instagram_access_token,
            ]);

            if ($response->successful()) {
                $newToken = $response->json()['access_token'];
                $setting->update(['instagram_access_token' => $newToken]);
                $this->info('Instagram token başarıyla yenilendi.');
            } else {
                $this->error('Token yenileme hatası: ' . $response->body());
            }
        }
    }
}