<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class MetaCapiService
{
    /**
     * Meta CAPI üzerinden event gönderir.
     */
    public static function sendEvent(string $eventName, array $userData = [], array $customData = [], ?string $eventId = null, ?string $testEventCode = null): void
    {
        // DB sorgusunu minimize etmek için ayarları çek
        $settings = Setting::first(); 
        if (!$settings || !$settings->facebook_pixel_code || !$settings->facebook_access_token) {
            return;
        }

        $pixelId = $settings->facebook_pixel_code;
        $accessToken = $settings->facebook_access_token;
        $apiVersion = 'v21.0';

        // 1. External ID Yönetimi (User Tracking)
        $externalId = request()->cookie('meta_ext_id') ?? $userData['external_id'] ?? null;
        if (!$externalId) {
            $externalId = (string) Str::uuid();
            Cookie::queue('meta_ext_id', $externalId, 525600); // 1 Yıl
        }

        // 2. IP Adresi (IPv6 Öncelikli ve Proxy Uyumlu)
        $clientIp = request()->header('X-Forwarded-For') ?? request()->ip();

        // 3. Veri Paketleme (Meta Standartlarına Uygun)
        $payload = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id'   => $eventId ?? (string) Str::uuid(),
                'action_source' => 'website',
                'event_source_url' => request()->fullUrl(),
                'user_data' => array_filter([
                    'client_ip_address' => $clientIp,
                    'client_user_agent' => request()->userAgent(),
                    'fbp' => request()->cookie('_fbp'),
                    'fbc' => self::getFbc(),
                    'external_id' => $externalId, // Hashlenmeden gönderilmesi önerilir
                    // Kişisel veriler hashlenerek gönderilir (EMQ artırır)
                    'em' => isset($userData['email']) ? self::hashData($userData['email']) : null,
                    'ph' => isset($userData['phone']) ? self::hashData($userData['phone'], true) : null,
                    'fn' => isset($userData['first_name']) ? self::hashData($userData['first_name']) : null,
                    'ln' => isset($userData['last_name']) ? self::hashData($userData['last_name']) : null,
                ]),
                'custom_data' => array_filter([
                    'value' => $customData['value'] ?? null,
                    'currency' => strtoupper($customData['currency'] ?? 'TRY'),
                    'content_name' => $customData['content_name'] ?? null,
                    'content_type' => $customData['content_type'] ?? 'product',
                    'content_ids' => $customData['content_ids'] ?? [],
                ]),
            ]],
        ];

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        // 4. Gönderim İşlemi
        try {
            Http::withToken($accessToken)
                ->timeout(5)
                ->post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events", $payload);
        } catch (\Exception $e) {
            Log::error("Meta CAPI Hatası: " . $e->getMessage());
        }
    }

    /**
     * fbc değerini URL'den veya cookie'den çeker/oluşturur.
     */
    private static function getFbc(): ?string
    {
        if ($fbc = request()->cookie('_fbc')) {
            return $fbc;
        }

        $fbclid = request()->query('fbclid');
        if ($fbclid) {
            // Meta format: fb.1.[TIMESTAMP].[FBCLID]
            return 'fb.1.' . time() . '.' . $fbclid;
        }

        return null;
    }

    /**
     * Meta standartlarında hashing (SHA256)
     */
    private static function hashData(?string $data, bool $isPhone = false): ?string
    {
        if (!$data) return null;

        $data = trim($data);
        $data = strtolower($data);

        if ($isPhone) {
            // Sadece rakamları bırak
            $data = preg_replace('/[^0-9]/', '', $data);
        }

        return hash('sha256', $data);
    }
}