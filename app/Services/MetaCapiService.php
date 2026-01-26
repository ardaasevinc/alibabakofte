<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $data = [], ?string $eventId = null)
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');
        $apiVersion  = 'v21.0';

        if (!$pixelId || !$accessToken) {
            Log::error('META_CAPI_CONFIG_MISSING', ['pixel_id' => $pixelId]);
            return null;
        }

        $eventId = $eventId ?? ($data['event_id'] ?? 'ev_' . Str::random(10) . '_' . time());
        $userData = $data['user_data'] ?? [];

        $userData['client_ip_address'] = $userData['client_ip_address'] ?? request()->ip();
        $userData['client_user_agent'] = $userData['client_user_agent'] ?? request()->userAgent();
        $userData['fbp'] = $userData['fbp'] ?? request()->cookie('_fbp');
        $userData['fbc'] = $userData['fbc'] ?? (request()->cookie('_fbc') ?? self::generateFbcFromUrl());

        if (!isset($userData['external_id'])) {
            $userData['external_id'] = hash('sha256', (string)session()->getId());
        }

        $userData = array_filter($userData, fn($value) => !is_null($value) && $value !== '');

        $customData = array_merge([
            'value' => 1.00,
            'currency' => 'TRY',
        ], $data['custom_data'] ?? []);

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
                ]
            ],
        ];

        if ($testCode = config('services.meta.test_code')) {
            $payload['test_event_code'] = $testCode;
        }

        $endpoint = "https://graph.facebook.com/{$apiVersion}/{$pixelId}/events";

        try {
            $response = Http::timeout(5)
                ->withToken($accessToken)
                ->post($endpoint, $payload);

            if ($response->failed()) {
                // IDE hatasını önlemek için collect() veya json() çıktısını değişkene alıyoruz
                $errorOutput = $response->json();

                Log::warning('META_CAPI_FAILED', [
                    'status' => $response->status(),
                    // null-safe operator (?) kullanarak iç içe dizi hatasını engelliyoruz
                    'error' => $errorOutput['error']['message'] ?? ($errorOutput['error'] ?? 'Unknown Meta Error'),
                    'event' => $eventName
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('META_CAPI_EXCEPTION', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private static function generateFbcFromUrl(): ?string
    {
        $fbclid = request()->query('fbclid') ?? session('fbclid');
        if (!$fbclid)
            return null;
        return 'fb.1.' . time() . '.' . $fbclid;
    }
}