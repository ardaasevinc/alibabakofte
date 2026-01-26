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
     * Trafik verilerini (utm, fbclid, fbp) session'a güvenli şekilde işler.
     */
    public static function captureTrafficDataOnce(Request $request): void
    {
        // fbclid yakalama
        if ($request->has('fbclid') && !Session::has('fbclid')) {
            Session::put('fbclid', $request->query('fbclid'));
        }

        // UTM parametreleri döngüsü
        $utms = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        foreach ($utms as $utm) {
            if ($request->has($utm) && !Session::has($utm)) {
                Session::put($utm, $request->query($utm));
            }
        }

        // FBP cookie'den session'a
        if ($request->cookie('_fbp') && !Session::has('_fbp')) {
            Session::put('_fbp', $request->cookie('_fbp'));
        }
    }

    public static function getOrCreateExternalId(Request $request): string
    {
        $id = $request->cookie('external_id') ?? (string) Str::uuid();
        if (!$request->hasCookie('external_id')) {
            Cookie::queue('external_id', $id, 60 * 24 * 365);
        }
        return $id;
    }

    public static function getOrCreateDeviceId(): string
    {
        $id = Cookie::get('device_id') ?? (string) Str::uuid();
        if (!Cookie::has('device_id')) {
            Cookie::queue('device_id', $id, 60 * 24 * 365);
        }
        return $id;
    }

    public static function getOrCreateBrowserId(): string
    {
        $id = Cookie::get('browser_id') ?? 'br_' . Str::random(24);
        if (!Cookie::has('browser_id')) {
            Cookie::queue('browser_id', $id, 60 * 24 * 365);
        }
        return $id;
    }

    public static function getOrCreateFbp(Request $request): string
    {
        $fbp = $request->cookie('_fbp') ?? 'fb.1.' . now()->timestamp . '.' . mt_rand(1000000000, 9999999999);
        if (!$request->hasCookie('_fbp')) {
            Cookie::queue('_fbp', $fbp, 60 * 24 * 365);
        }
        return $fbp;
    }

    public static function getFormattedFbc(Request $request): ?string
    {
        $fbclid = $request->query('fbclid') ?? Session::get('fbclid');
        return $fbclid ? 'fb.1.' . now()->timestamp . '.' . $fbclid : null;
    }

    public static function detectPlatform(string $ua): string
    {
        $ua = strtolower($ua);
        return match (true) {
            str_contains($ua, 'iphone'), str_contains($ua, 'ipad') => 'iOS',
            str_contains($ua, 'android') => 'Android',
            default => 'Desktop',
        };
    }

    public static function isMobileDevice(string $ua): bool
    {
        $ua = strtolower($ua);
        return str_contains($ua, 'iphone') || str_contains($ua, 'android') || str_contains($ua, 'mobile');
    }

    public static function generateEventId(): string
    {
        return 'evt_' . Str::random(12) . '_' . now()->timestamp;
    }

    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId = config('services.meta.pixel_id');
        $token = config('services.meta.access_token');
        $testCode = config('services.meta.test_event_code');

        if (!$pixelId || !$token) return;

        $request = request();
        $eventId ??= self::generateEventId();
        
        // Önemli: fbp, fbc, ip ve ua hash'lenmez. Diğerleri hash'lenir.
        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_id' => $eventId,
                    'action_source' => 'website',
                    'event_source_url' => $request->fullUrl(),
                    'user_data' => array_filter([
                        'client_ip_address' => $request->ip(),
                        'client_user_agent' => $request->userAgent(),
                        'fbp' => self::getOrCreateFbp($request),
                        'fbc' => self::getFormattedFbc($request),
                        'external_id' => hash('sha256', self::getOrCreateExternalId($request)),
                        'subscription_id' => hash('sha256', self::getOrCreateDeviceId()),
                        'browser_id' => hash('sha256', self::getOrCreateBrowserId()),
                    ]),
                    'custom_data' => array_merge([
                        'currency' => 'USD',
                        'value' => 0.00
                    ], $customData),
                ],
            ],
        ];

        if ($testCode) $payload['test_event_code'] = $testCode;

        try {
            Http::withToken($token)->post("https://graph.facebook.com/v19.0/{$pixelId}/events", $payload);
        } catch (\Throwable $e) {
            Log::error('MetaCapi Exception: ' . $e->getMessage());
        }
    }
}