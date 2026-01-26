<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Laravel 12 Helpers

class MetaCapiService
{
    /**
     * En yüksek kalite CAPI Event Gönderimi
     */
    public static function sendEvent(string $eventName, array $data = [], ?string $eventId = null)
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');
        $apiVersion  = 'v21.0';

        if (!$pixelId || !$accessToken) {
            Log::error('META_CAPI_CONFIG_MISSING', ['pixel_id' => $pixelId]);
            return null;
        }

        /* ============================================================
         * 1) Deduplication ID (Tekilleştirme)
         * ============================================================ */
        $eventId = $eventId ?? ($data['event_id'] ?? 'ev_' . Str::random(10) . '_' . time());

        /* ============================================================
         * 2) USER DATA (Gelişmiş Eşleştirme)
         * ============================================================ */
        $userData = $data['user_data'] ?? [];

        // Meta'nın beklediği formatta IP ve UA temizliği
        $userData['client_ip_address'] = $userData['client_ip_address'] ?? request()->ip();
        $userData['client_user_agent'] = $userData['client_user_agent'] ?? request()->userAgent();

        // FBP ve FBC kontrolü (Cookie üzerinden öncelikli)
        $userData['fbp'] = $userData['fbp'] ?? request()->cookie('_fbp');
        $userData['fbc'] = $userData['fbc'] ?? (request()->cookie('_fbc') ?? self::generateFbcFromUrl());

        if (!isset($userData['external_id'])) {
            $userData['external_id'] = hash('sha256', (string)session()->getId());
        }

        // Değerlerin boş (null veya empty string) gitmesini engelle (Meta hata verebilir)
        $userData = array_filter($userData, fn($value) => !is_null($value) && $value !== '');

        /* ============================================================
         * 3) CUSTOM DATA
         * ============================================================ */
        $customData = array_merge([
            'value' => 1.00,
            'currency' => 'TRY',
        ], $data['custom_data'] ?? []);

        /* ============================================================
         * 4) PAYLOAD
         * ============================================================ */
        $eventPayload = [
            'event_name' => $eventName,
            'event_time' => $data['event_time'] ?? time(),
            'event_id' => $eventId,
            'action_source' => 'website',
            'event_source_url' => $data['event_source_url'] ?? strtok(request()->fullUrl(), '?'),
            'user_data' => $userData,
            'custom_data' => $customData,
        ];

        $payload = [
            'data' => [$eventPayload],
        ];

        // Test kodu varsa ekle (Sadece debug sırasında)
        if ($testCode = config('services.meta.test_code')) {
            $payload['test_event_code'] = $testCode;
        }

        /* ============================================================
         * 5) SEND REQUEST
         * ============================================================ */
        $endpoint = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events";

        try {
            // Asenkron gönderim performansı artırır ancak loglamak için bekliyoruz
            $response = Http::timeout(5)
                ->withToken($accessToken) // Bearer Token kullanımı daha temizdir
                ->post($endpoint, $payload);

            if ($response->failed()) {
                Log::warning('META_CAPI_FAILED', [
                    'status' => $response->status(),
                    'error' => $response->json()['error'] ?? 'Unknown Error',
                    'event' => $eventName
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('META_CAPI_EXCEPTION', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * fbclid üzerinden fbc oluşturur
     */
    private static function generateFbcFromUrl(): ?string
    {
        $fbclid = request()->query('fbclid') ?? session('fbclid');
        if (!$fbclid) return null;

        // Versiyon.Index.Time.ClickId
        return 'fb.1.' . time() . '.' . $fbclid;
    }
}