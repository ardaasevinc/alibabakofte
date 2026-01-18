<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null, ?string $testEventCode = null): void
    {
        // DB sorgusunu minimize etmek için cache veya static değişken kullanılabilir
        $settings = Setting::first(); 
        if (!$settings || !$settings->facebook_pixel_code || !$settings->facebook_access_token) return;

        $pixelId = $settings->facebook_pixel_code;
        $accessToken = $settings->facebook_access_token;
        $apiVersion = 'v21.0';

        // 1. Gelişmiş External ID Mantığı
        // DB'de yok ama tarayıcıda 'kalıcı iz' bırakıyoruz.
        $externalId = request()->cookie('meta_ext_id') ?? (string) Str::uuid();
        if (!request()->hasCookie('meta_ext_id')) {
            Cookie::queue('meta_ext_id', $externalId, 525600); // 1 Yıl kalıcı
        }

        // 2. Veri Normalizasyonu ve Paketleme
        $payload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id'   => $eventId ?? (string) Str::uuid(),
                'action_source' => 'website',
                'event_source_url' => request()->fullUrl(),
                'user_data' => array_filter([
                    'client_ip_address' => request()->ip(),
                    'client_user_agent' => request()->userAgent(),
                    'fbp' => request()->cookie('_fbp'),
                    'fbc' => request()->cookie('_fbc') ?? self::generateFbc(),
                    // Meta her zaman küçük harf ve hashlenmiş bekler
                    'external_id' => hash('sha256', strtolower(trim($externalId))),
                ]),
                // Custom data puanı değil, reklam optimizasyonunu artırır
                'custom_data' => array_filter([
                    'value' => $customData['value'] ?? null,
                    'currency' => $customData['currency'] ?? 'TRY',
                    'content_name' => $customData['title'] ?? null,
                    'content_type' => 'product',
                ]),
            ]],
        ];

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        // 3. Gönderim (Hızlı ve Güvenli)
        try {
            Http::withToken($accessToken)
                ->timeout(3)
                ->post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events", $payload);
        } catch (\Exception $e) {
            Log::warning("Meta CAPI Gecikmesi: " . $e->getMessage());
        }
    }

    private static function generateFbc(): ?string
    {
        $fbclid = request()->query('fbclid');
        if (!$fbclid) return null;

        // Meta formatı: fb.1.[TIMESTAMP].[FBCLID]
        return 'fb.1.' . time() . '.' . $fbclid;
    }
}