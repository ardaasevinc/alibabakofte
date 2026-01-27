<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class MetaCapiService
{
    /**
     * En yüksek kalite CAPI Event Gönderimi
     * * @param string $eventName
     * @param array $data
     * @param string|null $eventId
     * @return Response|null
     */
    public static function sendEvent(string $eventName, array $data = [], ?string $eventId = null): ?Response
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');
        $apiVersion  = 'v21.0';

        if (!$pixelId || !$accessToken) {
            Log::error('META_CAPI_CONFIG_MISSING', ['pixel_id' => $pixelId]);
            return null;
        }

        /* ============================================================
         * 1) DEDUPLICATION ID (Tekilleştirme Anahtarı)
         * ============================================================ */
        // Evrensel Standart: JS'den gelen ID varsa o kullanılır.
        $eventId = $eventId ?? ($data['event_id'] ?? 'ev_auto_' . str()->random(10) . '_' . time());

        /* ============================================================
         * 2) USER DATA (Gelişmiş Eşleştirme)
         * ============================================================ */
        $userData = $data['user_data'] ?? [];

        // Meta'nın beklediği teknik veriler
        $userData['client_ip_address'] = $userData['client_ip_address'] ?? request()->ip();
        $userData['client_user_agent'] = $userData['client_user_agent'] ?? request()->userAgent();
        
        // FBP ve FBC (Eşleşme puanını %20-30 artırır)
        $userData['fbp'] = $userData['fbp'] ?? request()->cookie('_fbp');
        $userData['fbc'] = $userData['fbc'] ?? (request()->cookie('_fbc') ?? self::generateFbcFromUrl());

        // EXTERNAL ID: Script tarafındaki hash yöntemiyle birebir aynı (sha256)
        if (!isset($userData['external_id'])) {
            $userData['external_id'] = hash('sha256', (string)session()->getId());
        }

        // Meta boş/null değerleri sevmez, temizliyoruz.
        $userData = array_filter($userData, fn($value) => !is_null($value) && $value !== '');

        /* ============================================================
         * 3) CUSTOM DATA (Değer ve Para Birimi Sabitleme)
         * ============================================================ */
        $customData = array_merge([
            'value'    => 1.00,
            'currency' => 'TRY',
        ], $data['custom_data'] ?? []);

        /* ============================================================
         * 4) PAYLOAD OLUŞTURMA
         * ============================================================ */
        $eventPayload = [
            'event_name'       => $eventName,
            'event_time'       => $data['event_time'] ?? time(),
            'event_id'         => $eventId,
            'action_source'    => 'website',
            'event_source_url' => $data['event_source_url'] ?? strtok(request()->fullUrl(), '?'),
            'user_data'        => $userData,
            'custom_data'      => $customData,
        ];

        $payload = ['data' => [$eventPayload]];

        // Test Olayları kodu varsa ekle (TESTXXXXX)
        if ($testCode = config('services.meta.test_code')) {
            $payload['test_event_code'] = $testCode;
        }

        /* ============================================================
         * 5) API İSTEĞİ
         * ============================================================ */
        $endpoint = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events";

        try {
            /** @var Response $response */
            $response = Http::timeout(10)
                ->withToken((string)$accessToken)
                ->post($endpoint, $payload);

            if ($response->failed()) {
                Log::warning('META_CAPI_FAILED', [
                    'status' => $response->status(),
                    'error'  => $response->json('error.message', 'Meta API Hatası'),
                    'event_id' => $eventId
                ]);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('META_CAPI_EXCEPTION', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * URL'deki fbclid üzerinden fbc oluşturur
     */
    private static function generateFbcFromUrl(): ?string
    {
        $fbclid = request()->query('fbclid') ?? session('fbclid');
        if (!$fbclid) return null;

        return 'fb.1.' . time() . '.' . $fbclid;
    }
}