<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    public static function sendEvent(string $eventName, array $userData, string $eventId)
    {
        $settings = Setting::first();
        if (!$settings || !$settings->facebook_pixel_id || !$settings->facebook_access_token) return;

        $url = "https://graph.facebook.com/v18.0/{$settings->facebook_pixel_id}/events";

        $data = [
            'data' => [[
                'event_name' => $eventName,
                'event_time' => time(),
                'event_id'   => $eventId,
                'action_source' => 'website',
                'event_source_url' => $userData['event_source_url'] ?? request()->url(),
                'user_data' => array_filter([
                    'client_ip_address' => $userData['client_ip_address'] ?? request()->ip(),
                    'client_user_agent' => $userData['client_user_agent'] ?? request()->userAgent(),
                    'fbc' => $userData['fbc'] ?? null,
                    'fbp' => request()->cookie('_fbp'),
                    'external_id' => $userData['external_id'] ?? null,
                ]),
            ]],
            'access_token' => $settings->facebook_access_token,
        ];

        try {
            $response = Http::post($url, $data);
            if ($response->failed()) Log::warning("CAPI Error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("CAPI Connection Error: " . $e->getMessage());
        }
    }
}