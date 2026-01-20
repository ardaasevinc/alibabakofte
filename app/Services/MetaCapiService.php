<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');

        if (! $pixelId || ! $accessToken) {
            Log::warning('Meta CAPI: Pixel ID veya Access Token tanımlı değil.');
            return;
        }

        // Trafik verilerini bir kere yakala (utm, fbclid, fbp vs.)
        self::captureTrafficDataOnce();

        $eventId ??= self::generateEventId();

        $event = [
            'event_name'       => $eventName,
            'event_time'       => now()->timestamp,
            'event_id'         => $eventId,
            'action_source'    => 'website',
            'event_source_url' => Request::fullUrl(),

            'user_data' => array_filter([
                'client_ip_address' => Request::ip(),
                'client_user_agent' => Request::userAgent(),
                'fbp'               => self::getOrCreateFbp(),
                'fbc'               => self::getFormattedFbc(),
                'external_id'       => hash('sha256', Session::getId()),
                'subscription_id'   => hash('sha256', self::getOrCreateDeviceId()),
                'browser_id'        => Cookie::get('browser_id'),
                'country'           => 'tr',
            ]),

            'custom_data' => $customData,
        ];

        // Test modu açıksa test_event_code ekle
        if ($testCode = config('services.meta.test_code')) {
            $event['test_event_code'] = $testCode;
        }

        $payload = [
            'data' => [ $event ],
        ];

        Http::async()
            ->withToken($accessToken)
            ->post("https://graph.facebook.com/v21.0/{$pixelId}/events", $payload)
            ->otherwise(function ($e) {
                Log::error("Meta CAPI Error: " . $e->getMessage());
            });
    }

    public static function generateEventId(): string
    {
        return 'evt_' . Str::random(12) . '_' . now()->timestamp;
    }

    public static function getOrCreateDeviceId(): string
    {
        $cookie = Cookie::get('device_id');

        if (! $cookie) {
            $id = Str::uuid()->toString();
            Cookie::queue('device_id', $id, 60 * 24 * 365); // 1 yıl
            return $id;
        }

        return $cookie;
    }

    public static function getSessionHash(): string
    {
        return hash('sha256', Session::getId());
    }

    public static function getOrCreateFbp(): string
    {
        $cookie = Cookie::get('_fbp');

        if (! $cookie) {
            $fbp = 'fb.1.' . now()->timestamp . '.' . mt_rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 60 * 24 * 365);
            Session::put('_fbp', $fbp);
            return $fbp;
        }

        return $cookie;
    }

    public static function getFormattedFbc(): ?string
    {
        $fbclid = Request::query('fbclid') ?? Session::get('meta_fbclid');
        return $fbclid ? 'fb.1.' . now()->timestamp . '.' . $fbclid : null;
    }

    private static function captureTrafficDataOnce(): void
    {
        if (Request::has('fbclid') && ! Session::has('meta_fbclid')) {
            Session::put('meta_fbclid', Request::query('fbclid'));
        }

        if (Request::has('utm_source') && ! Session::has('utm_source')) {
            Session::put('utm_source', Request::query('utm_source'));
            Session::put('utm_campaign', Request::query('utm_campaign'));
            Session::put('utm_medium', Request::query('utm_medium'));
        }

        if (Request::cookie('_fbp') && ! Session::has('_fbp')) {
            Session::put('_fbp', Request::cookie('_fbp'));
        }
    }
}
