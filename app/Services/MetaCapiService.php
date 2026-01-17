<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    // 4. Parametre olarak test kodunu ekledik
    public static function sendEvent(string $eventName, array $userData, string $eventId, string $testEventCode = null)
    {
        // Verileri artık config (.env) üzerinden çekiyoruz
        $pixelId = config('services.meta.pixel_id') ?? Setting::first()?->facebook_pixel_code;
        $accessToken = config('services.meta.access_token') ?? Setting::first()?->facebook_access_token;

        if (!$pixelId || !$accessToken) {
            Log::warning("CAPI Hatası: Pixel ID veya Access Token eksik.");
            return;
        }

        $url = "https://graph.facebook.com/v18.0/{$pixelId}/events";

        $data = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id'   => $eventId,
                'action_source' => 'website',
                'event_source_url' => $userData['event_source_url'] ?? request()->url(),
                'user_data' => array_filter([
                    'client_ip_address' => request()->ip(),
                    'client_user_agent' => request()->userAgent(),
                    'fbc' => $userData['fbc'] ?? null,
                    'fbp' => request()->cookie('_fbp'),
                    'external_id' => $userData['external_id'] ?? null,
                ]),
            ]],
            'access_token' => $accessToken,
        ];

        // Eğer test kodu varsa gönderiye ekliyoruz
        if ($testEventCode) {
            $data['test_event_code'] = $testEventCode;
        }

        try {
            $response = Http::post($url, $data);
            if ($response->failed()) Log::warning("CAPI Yanıt Hatası: " . $response->body());
        } catch (\Exception $e) {
            Log::error("CAPI Bağlantı Hatası: " . $e->getMessage());
        }
    }
}