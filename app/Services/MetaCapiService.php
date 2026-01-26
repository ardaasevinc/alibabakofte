<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        /* ============================================================
         * 1) Deduplication ID
         * ============================================================ */
        $eventId = $eventId ?? ($data['event_id'] ?? 'event_' . bin2hex(random_bytes(6)) . '_' . time());

        /* ============================================================
         * 2) USER DATA (Controller'dan gelen veriyi koru ve genişlet)
         * ============================================================ */
        // Controller'dan gelen user_data varsa onu al, yoksa boş dizi oluştur
        $userData = $data['user_data'] ?? [];

        // Eksik temel verileri tamamla
        $userData['fbp'] = $userData['fbp'] ?? request()->cookie('_fbp');
        $userData['fbc'] = $userData['fbc'] ?? (request()->cookie('_fbc') ?? self::generateFbcFromUrl());
        $userData['client_ip_address'] = $userData['client_ip_address'] ?? request()->ip();
        $userData['client_user_agent'] = $userData['client_user_agent'] ?? request()->userAgent();
        
        if (!isset($userData['external_id'])) {
            $userData['external_id'] = hash('sha256', (string)session()->getId());
        }

        // null değerleri temizle
        $userData = array_filter($userData);

        /* ============================================================
         * 3) CUSTOM DATA (Para birimi ve Değer hatasını çözer)
         * ============================================================ */
        $customData = $data['custom_data'] ?? [];

        $customData = array_merge([
            'value' => 1.00, // Sayısal (float) gönderim kritik
            'currency' => 'TRY',
        ], $customData);

        /* ============================================================
         * 4) PAYLOAD — Meta Official Format
         * ============================================================ */
        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => $data['event_time'] ?? time(),
                    'event_id' => $eventId,
                    'action_source' => 'website',
                    'event_source_url' => $data['event_source_url'] ?? strtok(request()->fullUrl(), '?'),

                    'user_data' => $userData,
                    'custom_data' => $customData,
                ],
            ],
            'test_event_code' => config('services.meta.test_code') ?? null,
        ];

        /* ============================================================
         * 5) SEND REQUEST
         * ============================================================ */
        $endpoint = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events?access_token={$accessToken}";

        try {
            $response = Http::timeout(10)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint, $payload);

            if ($response->failed()) {
                Log::warning('META_CAPI_FAILED', [
                    'status' => $response->status(),
                    'body' => $response->json()
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
        $fbclid = request()->query('fbclid');
        if (!$fbclid)
            return null;

        return 'fb.1.' . time() . '.' . $fbclid;
    }
}