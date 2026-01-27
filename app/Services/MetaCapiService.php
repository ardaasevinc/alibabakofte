<?php

namespace App\Services;

use Illuminate\Support\Facades\{Http, Log, Auth};

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $data = []): void
    {
        $pixelId     = config('services.meta.pixel_id');
        $accessToken = config('services.meta.access_token');
        $apiVersion  = 'v21.0';

        if (!$pixelId || !$accessToken) {
            Log::error('META_CAPI_CONFIG_MISSING: Pixel ID veya Access Token eksik.');
            return;
        }

        // 1. EVENT TIME (Gelecek/Geçmiş hatasını önlemek için tam saniye)
        $eventTime = time();

        // 2. USER DATA
        $userData = [
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
            'fbp'               => request()->cookie('_fbp'),
            'fbc'               => request()->cookie('_fbc'),
            'external_id'       => hash('sha256', session()->getId()),
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $userData['em'] = [hash('sha256', strtolower(trim($user->email)))];
        }

        // 3. CUSTOM DATA (Dinamik fiyat)
        $customData = array_merge([
            'value'    => $data['value'] ?? (float) (1.00 + (rand(1, 99) / 100)),
            'currency' => 'TRY',
        ], $data['custom_data'] ?? []);

        // 4. PAYLOAD
        $eventPayload = [
            'event_name'       => $eventName,
            'event_time'       => $eventTime,
            'event_id'         => $data['event_id'],
            'action_source'    => 'website',
            'event_source_url' => $data['event_source_url'] ?? strtok(request()->fullUrl(), '?'),
            'user_data'        => array_filter($userData),
            'custom_data'      => array_filter($customData),
        ];

        $payload = ['data' => [$eventPayload]];

        // TEST KODU: Meta panelindeki TEST78720 değerini buraya veya .env'ye yaz
        $testCode = config('services.meta.test_code') ?? 'TEST78720';
        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        // 5. SENKRON GÖNDERİM VE LOGLAMA (Hata tespiti için geçici olarak dispatch kaldırıldı)
        try {
            $response = Http::withToken((string)$accessToken)
                ->timeout(15)
                ->post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events", $payload);

            Log::info('Meta CAPI İşlemi', [
                'event' => $eventName,
                'status' => $response->status(),
                'response' => $response->json(),
                'event_id' => $data['event_id']
            ]);

        } catch (\Exception $e) {
            Log::error('Meta CAPI Exception: ' . $e->getMessage());
        }
    }
}