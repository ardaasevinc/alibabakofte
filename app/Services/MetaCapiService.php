<?php

namespace App\Services;

use Illuminate\Support\Facades\{Http, Log, Session, Request};
use Illuminate\Support\Str;

class MetaCapiService
{
    /**
     * Meta Event Gönderimi (Asenkron)
     */
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');

        // Laravel 12 Fluent String kullanımı
        $eventId ??= (string) Str::of('ab_')
            ->append(Str::random(8))
            ->append('_')
            ->append(now()->timestamp);

        // Trafik verilerini session'a sabitle (Eğer URL'de varsa)
        self::captureTrafficData();

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => now()->timestamp,
                    'action_source' => 'website',
                    'event_id' => $eventId,
                    'event_source_url' => Request::fullUrl(),
                    'user_data' => array_filter([
                        'client_ip_address' => Request::ip(),
                        'client_user_agent' => Request::userAgent(),
                        'fbp' => Request::cookie('_fbp') ?? Session::get('_fbp'),
                        'fbc' => self::getFormattedFbc(),
                        'external_id' => hash('sha256', Session::getId()),
                    ]),
                    'custom_data' => $customData,
                ],
            ],
        ];

        // Laravel 12 asenkron HTTP istemcisi (Redirect hızını etkilemez)
        Http::async()
            ->withToken($accessToken)
            ->post("https://graph.facebook.com/v21.0/{$pixelId}/events", $payload)
            ->otherwise(fn ($e) => Log::error("Meta CAPI Error: " . $e->getMessage()));
    }

    /**
     * Trafik Verilerini Session'da Kilitle
     */
    public static function captureTrafficData(): void
    {
        // URL'de fbclid varsa session'a yaz (Kalıcılık sağlar)
        if (Request::has('fbclid')) {
            Session::put('meta_fbclid', Request::query('fbclid'));
        }

        // UTM verilerini session'da sakla
        if (Request::has('utm_source')) {
            Session::put('utm_source', Request::query('utm_source'));
            Session::put('utm_campaign', Request::query('utm_campaign', 'tanimsiz'));
        }
    }

    /**
     * Veritabanı Kaydı İçin Kaynak Tespiti
     */
    public static function getDetectedSource(): string
    {
        if (Session::has('utm_source')) {
            return (string) Session::get('utm_source');
        }

        return Session::has('meta_fbclid') ? 'facebook_ad' : 'direct';
    }

    /**
     * FBC Parametresini Formatla
     */
    private static function getFormattedFbc(): ?string
    {
        $fbclid = Request::query('fbclid') ?? Session::get('meta_fbclid');
        
        return $fbclid 
            ? "fb.1." . now()->timestamp . ".{$fbclid}" 
            : null;
    }
}