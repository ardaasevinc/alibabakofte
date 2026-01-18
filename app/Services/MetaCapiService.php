<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $userData = [], array $customData = [], ?string $eventId = null, ?string $testEventCode = null): void
    {
        $settings = Setting::first(); 
        if (!$settings || !$settings->facebook_pixel_code || !$settings->facebook_access_token) return;

        $pixelId = $settings->facebook_pixel_code;
        $accessToken = $settings->facebook_access_token;
        $apiVersion = 'v21.0';

        // 1. External ID: Varsa gönderilen, yoksa cookie, o da yoksa yeni UUID
        $externalId = $userData['external_id'] ?? request()->cookie('meta_ext_id');
        if (!$externalId) {
            $externalId = (string) Str::uuid();
            Cookie::queue('meta_ext_id', $externalId, 525600);
        }

        // 2. Payload Hazırlama
        $payload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id'   => $eventId ?? (string) Str::uuid(),
                'action_source' => 'website',
                'event_source_url' => request()->header('referer') ?? request()->fullUrl(),
                'user_data' => array_filter([
                    'client_ip_address' => request()->header('X-Forwarded-For') ?? request()->ip(),
                    'client_user_agent' => request()->userAgent(),
                    'fbp' => request()->cookie('_fbp'),
                    'fbc' => self::getFbc(),
                    'external_id' => $externalId, 
                    // PII Verileri (Otomatik Hashlenir)
                    'em' => isset($userData['email']) ? self::hashData($userData['email']) : null,
                    'ph' => isset($userData['phone']) ? self::hashData($userData['phone'], true) : null,
                ]),
                'custom_data' => array_filter([
                    'value' => $customData['value'] ?? null,
                    'currency' => strtoupper($customData['currency'] ?? 'TRY'),
                    'content_name' => $customData['content_name'] ?? null,
                    'content_type' => $customData['content_type'] ?? 'product',
                ]),
            ]],
        ];

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        // 3. Gönderim
        try {
            Http::withToken($accessToken)->timeout(5)
                ->post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events", $payload);
        } catch (\Exception $e) {
            Log::warning("Meta CAPI Hatası: " . $e->getMessage());
        }
    }

    private static function getFbc(): ?string
    {
        $fbc = request()->cookie('_fbc');
        if ($fbc) return $fbc;

        $fbclid = request()->query('fbclid');
        return $fbclid ? 'fb.1.' . time() . '.' . $fbclid : null;
    }

    private static function hashData(?string $data, bool $isPhone = false): ?string
    {
        if (!$data) return null;
        $data = strtolower(trim($data));
        if ($isPhone) $data = preg_replace('/[^0-9]/', '', $data);
        return hash('sha256', $data);
    }
}