<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $userData, string $eventId, string $testEventCode = null)
    {
        $settings = Setting::first();
        $pixelId = $settings->facebook_pixel_code; 
        $accessToken = $settings->facebook_access_token;

        if (!$pixelId || !$accessToken) return;

        $url = "https://graph.facebook.com/v18.0/{$pixelId}/events";

        $data = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id'   => $eventId, // Tarayıcı ile eşleşme için kritik
                'action_source' => 'website',
                'event_source_url' => $userData['event_source_url'] ?? request()->url(),
                'user_data' => array_filter([
                    'client_ip_address' => request()->ip(),
                    'client_user_agent' => request()->userAgent(),
                    'fbc' => $userData['fbc'] ?? null,
                    'fbp' => request()->cookie('_fbp'),
                    'external_id' => $userData['external_id'] ?? null,
                ]),
            ]],
            'access_token' => $accessToken,
        ];

        if ($testEventCode) {
            $data['test_event_code'] = $testEventCode;
        }

        try {
            // timeout(5) ekledik ki Meta yavaşsa site kilitlenmesin
            Http::timeout(5)->post($url, $data);
        } catch (\Exception $e) {
            Log::error("CAPI Hatası: " . $e->getMessage());
        }
    }
}