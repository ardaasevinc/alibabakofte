<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MetaCapiService
{
    /**
     * Trafik verilerini 1 kez yakala (utm, fbclid, fbp)
     */
    public static function captureTrafficDataOnce(Request $request): void
    {
        if ($request->has('fbclid') && ! Session::has('fbclid')) {
            Session::put('fbclid', $request->query('fbclid'));
        }

        foreach (['utm_source','utm_medium','utm_campaign','utm_term','utm_content'] as $utm) {
            if ($request->has($utm) && ! Session::has($utm)) {
                Session::put($utm, $request->query($utm));
            }
        }

        if ($request->cookie('_fbp') && ! Session::has('_fbp')) {
            Session::put('_fbp', $request->cookie('_fbp'));
        }
    }


    /**
     * External ID
     */
    public static function getOrCreateExternalId(Request $request): string
    {
        $cookie = $request->cookie('external_id');

        if (! $cookie) {
            $id = base64_encode(now()->timestamp . '_' . Str::random(16));
            Cookie::queue('external_id', $id, 60 * 24 * 365);
            return $id;
        }

        return $cookie;
    }


    /**
     * Device ID
     */
    public static function getOrCreateDeviceId(): string
    {
        $cookie = Cookie::get('device_id');

        if (! $cookie) {
            $id = (string) Str::uuid();
            Cookie::queue('device_id', $id, 60 * 24 * 365);
            return $id;
        }

        return $cookie;
    }


    /**
     * Browser ID
     */
    public static function getOrCreateBrowserId(): string
    {
        $cookie = Cookie::get('browser_id');

        if (! $cookie) {
            $id = 'br_' . Str::random(24);
            Cookie::queue('browser_id', $id, 60 * 24 * 365);
            return $id;
        }

        return $cookie;
    }


    /**
     * FBP
     */
    public static function getOrCreateFbp(Request $request): string
    {
        $cookie = $request->cookie('_fbp');

        if (! $cookie) {
            $fbp = 'fb.1.' . now()->timestamp . '.' . mt_rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 60 * 24 * 365);
            return $fbp;
        }

        return $cookie;
    }


    /**
     * FBC
     */
    public static function getFormattedFbc(Request $request): ?string
    {
        $fbclid = $request->query('fbclid') ?? Session::get('fbclid');

        return $fbclid
            ? 'fb.1.' . now()->timestamp . '.' . $fbclid
            : null;
    }


    /**
     * Platform (iOS / Android / Desktop)
     */
    public static function detectPlatform(string $ua): string
    {
        $ua = strtolower($ua);

        return match (true) {
            str_contains($ua, 'iphone'),
            str_contains($ua, 'ipad')  => 'iOS',

            str_contains($ua, 'android') => 'Android',

            default => 'Desktop'
        };
    }


    /**
     * Cihaz mobil mi?
     */
    public static function isMobileDevice(string $ua): bool
    {
        $ua = strtolower($ua);

        return str_contains($ua, 'iphone')
            || str_contains($ua, 'android')
            || str_contains($ua, 'ipad')
            || str_contains($ua, 'mobile');
    }


    /**
     * Event ID
     */
    public static function generateEventId(): string
    {
        return 'evt_' . Str::random(12) . '_' . now()->timestamp;
    }


    /**
     * CAPI request
     */
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');

        if (! $pixelId || ! $accessToken) {
            return;
        }

        $request     = request();
        $eventId   ??= self::generateEventId();

        $payload = [
            'data' => [
                [
                    'event_name'       => $eventName,
                    'event_time'       => time(),
                    'event_id'         => $eventId,
                    'action_source'    => 'website',
                    'event_source_url' => $request->fullUrl(),

                    'user_data' => array_filter([
                        'client_ip_address' => $request->ip(),
                        'client_user_agent' => $request->userAgent(),
                        'fbp'               => self::getOrCreateFbp($request),
                        'fbc'               => self::getFormattedFbc($request),
                        'external_id'       => hash('sha256', self::getOrCreateExternalId($request)),
                        'subscription_id'   => hash('sha256', self::getOrCreateDeviceId()),
                        'browser_id'        => hash('sha256', self::getOrCreateBrowserId()),
                        'country'           => 'tr',
                    ]),

                    'custom_data' => $customData,
                ],
            ],
        ];

        try {
            Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v21.0/{$pixelId}/events", $payload);
        } catch (\Throwable $e) {
            Log::error('MetaCapi Error', ['error' => $e->getMessage()]);
        }
    }
}
