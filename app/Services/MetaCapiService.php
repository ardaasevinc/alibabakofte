<?php

namespace App\Services;

use Illuminate\Support\Facades\{Http, Log, Session, Request, Cookie};
use Illuminate\Support\Str;

class MetaCapiService
{
    /**
     * Meta Event Gönderimi (Advanced Matching)
     */
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');

        // Event ID (deduplication)
        $eventId ??= 'evt_' . Str::random(10) . '_' . now()->timestamp;

        // Trafik verilerini sadece bir kez sakla
        self::captureTrafficDataOnce();

        // Benzersiz cihaz ID (cookie tabanlı)
        $deviceId = self::getDeviceId();

        // Session ID (hash)
        $sessionHash = hash('sha256', Session::getId());

        // FBP cookie
        $fbp = Request::cookie('_fbp') ?? Session::get('_fbp');

        // FBC
        $fbc = self::getFormattedFbc();

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

                        // En yüksek eşleşme gücü
                        'fbp' => $fbp,
                        'fbc' => $fbc,
                        'external_id' => $sessionHash,
                        'subscription_id' => $deviceId,
                       
                    ]),

                    'custom_data' => $customData,
                ],
            ],
        ];

        Http::async()
            ->withToken($accessToken)
            ->post("https://graph.facebook.com/v21.0/{$pixelId}/events", $payload)
            ->otherwise(fn($e) => Log::error("Meta CAPI Error: " . $e->getMessage()));
    }

    /**
     * Trafik verilerini sadece ilk girişte yaz.
     */
    private static function captureTrafficDataOnce(): void
    {
        // fbclid 1 kere yaz
        if (Request::has('fbclid') && !Session::has('meta_fbclid')) {
            Session::put('meta_fbclid', Request::query('fbclid'));
        }

        // UTM sadece 1 kere yaz
        if (Request::has('utm_source') && !Session::has('utm_source')) {
            Session::put('utm_source', Request::query('utm_source'));
            Session::put('utm_campaign', Request::query('utm_campaign', 'organic'));
        }

        // FBP
        if (Request::cookie('_fbp') && !Session::has('_fbp')) {
            Session::put('_fbp', Request::cookie('_fbp'));
        }
    }

    /**
     * Formatlı FBC verisi
     */
    private static function getFormattedFbc(): ?string
    {
        $fbclid = Request::query('fbclid') ?? Session::get('meta_fbclid');
        return $fbclid ? "fb.1." . now()->timestamp . ".{$fbclid}" : null;
    }

    /**
     * Cihaz ID: Kullanıcıya ait kalıcı benzersiz ID
     */
    private static function getDeviceId(): string
    {
        if (!Cookie::get('device_id')) {
            $deviceId = Str::uuid()->toString();
            Cookie::queue('device_id', $deviceId, 60 * 24 * 365); // 1 yıl
            return $deviceId;
        }

        return Cookie::get('device_id');
    }
}
