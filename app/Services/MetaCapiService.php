<?php

namespace App\Services;

use Illuminate\Support\Facades\{Http, Log, Session, Request, Cookie};
use Illuminate\Support\Str;

class MetaCapiService
{
    /**
     * Meta Event Gönderimi
     */
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');

        self::captureTrafficDataOnce();

        $eventId ??= self::generateEventId();

        $payload = [
            'data' => [
                [
                    'event_name'       => $eventName,
                    'event_time'       => now()->timestamp,
                    'event_id'         => $eventId,
                    'action_source'    => 'website',
                    'event_source_url' => Request::headers()->get('referer') ?? Request::fullUrl(),

                    'user_data' => array_filter([
                        'client_ip_address' => Request::ip(),
                        'client_user_agent' => Request::userAgent(),
                        'fbp'               => self::getOrCreateFbp(),
                        'fbc'               => self::getFormattedFbc(),
                        'external_id'       => hash('sha256', Session::getId()),
                        'subscription_id'   => hash('sha256', self::getOrCreateDeviceId()),
                        'browser_id'        => Cookie::get('browser_id'),
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
     * Benzersiz Event ID
     */
    public static function generateEventId(): string
    {
        return 'evt_' . Str::random(12) . '_' . now()->timestamp;
    }

    /**
     * Benzersiz Device ID
     */
    public static function getOrCreateDeviceId(): string
    {
        if (!Cookie::get('device_id')) {
            $id = Str::uuid()->toString();
            Cookie::queue('device_id', $id, 60 * 24 * 365);
            return $id;
        }
        return Cookie::get('device_id');
    }

    /**
     * FBP oluşturma
     */
    public static function getOrCreateFbp(): string
    {
        if (!Cookie::get('_fbp')) {
            $fbp = 'fb.1.' . now()->timestamp . '.' . mt_rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 60 * 24 * 365);
            Session::put('_fbp', $fbp);
            return $fbp;
        }
        return Cookie::get('_fbp');
    }

    /**
     * FBC Formatı
     */
    public static function getFormattedFbc(): ?string
    {
        $fbclid = Request::query('fbclid') ?? Session::get('meta_fbclid');
        return $fbclid ? "fb.1." . now()->timestamp . ".{$fbclid}" : null;
    }

    /**
     * UTM + FBCLID + FBP sadece ilk girişte yazılır
     */
    private static function captureTrafficDataOnce(): void
    {
        if (Request::has('fbclid') && !Session::has('meta_fbclid')) {
            Session::put('meta_fbclid', Request::query('fbclid'));
        }

        if (Request::has('utm_source') && !Session::has('utm_source')) {
            Session::put('utm_source', Request::query('utm_source'));
            Session::put('utm_campaign', Request::query('utm_campaign'));
            Session::put('utm_medium', Request::query('utm_medium'));
        }

        if (Request::cookie('_fbp') && !Session::has('_fbp')) {
            Session::put('_fbp', Request::cookie('_fbp'));
        }
    }
}
