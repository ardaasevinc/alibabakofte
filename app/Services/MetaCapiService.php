<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    /**
     * Meta Event Builder & Sender
     */
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null)
    {
        $pixelId = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');
        $apiVersion = 'v21.0';

        // Tekilleştirme: Hem Browser hem Server tarafında aynı ID gitmeli
        $eventId = $eventId ?? (string) Str::uuid();

        // 1. Önce URL'den gelen parametreleri yakala ve session'a at (Kalıcılık için)
        self::captureTrafficData();

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'action_source' => 'website',
                    'event_id' => $eventId,
                    'event_source_url' => request()->fullUrl(),
                    'user_data' => array_filter([
                        // IP ve User Agent her zaman kritik
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                        
                        // Meta Çerezleri: Önce çereze bak, yoksa az önce yakaladığımız session'a bak
                        'fbp' => request()->cookie('_fbp') ?? session('meta_fbp'),
                        'fbc' => request()->cookie('_fbc') ?? self::getFormattedFbc(),
                        
                        // Oturum açmayan kullanıcı için en stabil kimlik: session_id hash
                        'external_id' => hash('sha256', session()->getId()),
                        
                        // Lokasyon Tahmini (Dil üzerinden)
                        'country' => self::hashData(substr(request()->getLanguages()[0] ?? 'tr', -2)), 
                    ]),
                    'custom_data' => array_merge($customData, [
                        'traffic_source' => self::getDetectedSource(), // Analiz için ekledik
                    ]),
                ],
            ],
        ];

        try {
            return Http::post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events?access_token={$accessToken}", $payload);
        } catch (\Exception $e) {
            Log::error("Meta CAPI Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * URL'deki fbclid ve UTM parametrelerini session'da saklar.
     * Bu sayede kullanıcı sayfalar arası gezse de "kaynak" kaybolmaz.
     */
    public static function captureTrafficData(): void
    {
        if (request()->has('fbclid')) {
            session(['meta_fbclid' => request()->query('fbclid')]);
        }

        if (request()->has('utm_source')) {
            session(['utm_source' => request()->query('utm_source')]);
            session(['utm_campaign' => request()->query('utm_campaign', '-')]);
        }
    }

    /**
     * Veritabanına kaydedilecek kaynağı belirler.
     */
    public static function getDetectedSource(): string
    {
        if (session('utm_source') === 'facebook' || session('meta_fbclid')) {
            return 'Facebook Ad';
        }
        
        if (request()->headers->get('referer')) {
            $host = parse_url(request()->headers->get('referer'), PHP_URL_HOST);
            if (str_contains($host, 'facebook.com') || str_contains($host, 'instagram.com')) {
                return 'Social Media';
            }
        }

        return 'direct';
    }

    private static function getFormattedFbc(): ?string
    {
        $fbclid = request()->query('fbclid') ?? session('meta_fbclid');
        return $fbclid ? 'fb.1.' . time() . '.' . $fbclid : null;
    }

    private static function hashData($data): ?string
    {
        return $data ? hash('sha256', strtolower(trim((string)$data))) : null;
    }
}