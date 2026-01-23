<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Str;

class MetaCapiService
{
    /**
     * Meta Event Gönderimi
     *
     * @param  string      $eventName
     * @param  array       $customData  Lead / PageView vb. özel datalar
     * @param  string|null $eventId     Deduplication için event_id
     */
    public static function sendEvent(string $eventName, array $customData = [], ?string $eventId = null): void
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');

        if (! $pixelId || ! $accessToken) {
            Log::warning('Meta CAPI: Pixel ID veya Access Token tanımlı değil.');
            return;
        }

        // Trafik verilerini (utm, fbclid, fbp) Session'a yaz
        self::captureTrafficDataOnce();

        $eventId ??= self::generateEventId();

        // Advanced Matching alanları
        $deviceId = self::getOrCreateDeviceId();
        $fbp = self::getOrCreateFbp();
        $fbc = self::getFormattedFbc();
        $browserId = Cookie::get('browser_id');

        $event = [
            'event_name'       => $eventName,
            'event_time'       => now()->timestamp,
            'event_id'         => $eventId,
            'action_source'    => 'website',
            'event_source_url' => RequestFacade::fullUrl(),

            'user_data' => array_filter([
                'client_ip_address' => RequestFacade::ip(),
                'client_user_agent' => RequestFacade::userAgent(),
                'fbp' => $fbp,
                'fbc' => $fbc,

                // Session + Device üzerinden güçlü deduplikasyon / advanced matching
                'external_id'       => hash('sha256', Session::getId()),
                'subscription_id' => hash('sha256', $deviceId),
                'browser_id' => $browserId,
                'country'           => 'tr',
            ]),

            'custom_data' => $customData,
        ];

        $payload = [
            'data' => [$event],
        ];

        // Test modu açıksa test_event_code ekle
        if ($testCode = config('services.meta.test_code')) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v21.0/{$pixelId}/events", $payload);

            $status = $response->status();

            // 200–299 dışındaki tüm durumları hata olarak logla
            if ($status < 200 || $status > 300) {
                Log::error('Meta CAPI Error Response', [
                    'status' => $status,
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Meta CAPI Exception: ' . $e->getMessage(), [
                'payload' => $payload,
            ]);
        }

    }

    /**
     * Event ID üretimi (deduplication için)
     */
    public static function generateEventId(): string
    {
        return 'evt_' . Str::random(12) . '_' . now()->timestamp;
    }

    /**
     * Cihaz ID'si (cookie üzerinden)
     */
    public static function getOrCreateDeviceId(): string
    {
        $cookie = Cookie::get('device_id');

        if (! $cookie) {
            $id = Str::uuid()->toString();
            // 1 yıl
            Cookie::queue('device_id', $id, 60 * 24 * 365);
            return $id;
        }

        return $cookie;
    }

    /**
     * Browser ID (ek bir kimlik, advanced matching için)
     */
    public static function getOrCreateBrowserId(): string
    {
        $id = Cookie::get('browser_id');

        if (!$id) {
            $id = 'br_' . Str::random(24);
            Cookie::queue('browser_id', $id, 60 * 24 * 365);
        }

        return $id;
    }

    /**
     * Session hash (external_id için kullanılabilir)
     */
    public static function getSessionHash(): string
    {
        return hash('sha256', Session::getId());
    }

    /**
     * FBP cookie (Facebook Browser ID)
     */
    public static function getOrCreateFbp(): string
    {
        $cookie = Cookie::get('_fbp');

        if (! $cookie) {
            $fbp = 'fb.1.' . now()->timestamp . '.' . mt_rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 60 * 24 * 365); // 1 yıl
            Session::put('_fbp', $fbp);

            return $fbp;
        }

        return $cookie;
    }

    /**
     * FBC parametresi (reklam tıklaması varsa)
     */
    public static function getFormattedFbc(): ?string
    {
        $fbclid = RequestFacade::query('fbclid') ?? Session::get('meta_fbclid');

        return $fbclid ? 'fb.1.' . now()->timestamp . '.' . $fbclid : null;
    }

    /**
     * Trafik verilerini (utm, fbclid, fbp) 1 kez yakala ve Session'a yaz
     */
    public static function captureTrafficDataOnce(): void
    {
        // fbclid
        if (RequestFacade::has('fbclid') && !Session::has('meta_fbclid')) {
            Session::put('meta_fbclid', RequestFacade::query('fbclid'));
        }

        // UTM'ler
        if (RequestFacade::has('utm_source') && !Session::has('utm_source')) {
            Session::put('utm_source', RequestFacade::query('utm_source'));
            Session::put('utm_campaign', RequestFacade::query('utm_campaign'));
            Session::put('utm_medium', RequestFacade::query('utm_medium'));
        }

        // FBP cookie'den session'a
        if (RequestFacade::cookie('_fbp') && !Session::has('_fbp')) {
            Session::put('_fbp', RequestFacade::cookie('_fbp'));
        }
    }
}
