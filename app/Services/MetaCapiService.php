<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null)
    {
        $pixelId = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');
        $apiVersion = 'v21.0';

        // Tekilleştirme ID'si (Pixel tarafıyla aynı gönderilmeli)
        $eventId = $eventId ?? (string) Str::uuid();

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'action_source' => 'website',
                    'event_id' => $eventId,
                    'event_source_url' => request()->fullUrl(),
                    'user_data' => array_filter([
                        // 1. Tarayıcı ve IP (Temel Eşleştirme)
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                        
                        // 2. Çerez İzleyicileri (En Yüksek Puanı Bunlar Verir)
                        'fbp' => request()->cookie('_fbp'), // Meta Browser ID
                        'fbc' => request()->cookie('_fbc') ?? self::getFbcFromUrl(), // Meta Click ID
                        
                        // 3. Anonim Kimlik (External ID)
                        // Form olmasa bile session_id'yi hashleyerek göndermek, 
                        // Meta'nın bu oturumu bir "birey" olarak gruplamasını sağlar.
                        'external_id' => hash('sha256', session()->getId()),
                        
                        // 4. Gelişmiş Tarayıcı Parametreleri
                        // Eğer dilde/bölgede özelleştirme varsa:
                        'country' => self::hashData(substr(request()->getLanguages()[0] ?? 'tr', -2)), 
                    ]),
                    'custom_data' => $customData,
                ],
            ],
        ];

        return Http::post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events?access_token={$accessToken}", $payload);
    }

    /**
     * Meta Click ID (fbclid) URL'den yakalama
     */
    private static function getFbcFromUrl(): ?string
    {
        $fbclid = request()->query('fbclid');
        if (!$fbclid) return null;

        // Format: fb.1.creation_time.fbclid
        return 'fb.1.' . time() . '.' . $fbclid;
    }

    private static function hashData($data): ?string
    {
        if (!$data) return null;
        return hash('sha256', strtolower(trim((string)$data)));
    }
}