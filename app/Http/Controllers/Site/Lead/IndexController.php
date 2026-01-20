<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;


class IndexController extends Controller
{
    /**
     * WhatsApp Butonu
     */
    public function whatsapp(Request $request)
    {
        return $this->processLead('meta-whatsapp', "https://wa.me/905352855696");
    }

    /**
     * Menü Butonu
     */
    public function menu(Request $request)
    {
        return $this->processLead('meta-menu', route('site.menu.index'));
    }

    /**
     * Ortak Lead Kayıt + CAPI Gönderimi
     */
    private function processLead(string $buttonId, string $targetUrl)
    {
        $ip = request()->ip();
        $agent = request()->userAgent();
        $type = $buttonId === 'meta-whatsapp' ? 'whatsapp' : 'menu';

        // Device & session tracking
        $sessionHash = hash('sha256', session()->getId());
        $rawDeviceId = MetaCapiService::getOrCreateDeviceId(); // raw device ID
        $deviceId = hash('sha256', $rawDeviceId);           // hashed for DB & CAPI
        $fbp = MetaCapiService::getOrCreateFbp();
        $fbc = MetaCapiService::getFormattedFbc();
        $browserId = Cookie::get('browser_id'); // JS ile yazılacak

        // URL kaynakları
        $previousUrl = url()->previous();
        $referer = request()->headers->get('referer');
        $urlData = parse_url($previousUrl);
        parse_str($urlData['query'] ?? '', $qs);

        // 24 saat duplication kontrolü
        $exists = Lead::where('type', $type)
            ->where('device_id', $deviceId)
            ->where('session_hash', $sessionHash)
            ->where('created_at', '>', now()->subHours(24))
            ->exists();

        if ($exists) {
            return redirect()->to($targetUrl); // CAPI gönderme
        }

        try {
            $eventId = MetaCapiService::generateEventId();

            $lead = Lead::create([
                'type' => $type,
                'event_id' => $eventId,
                'event_name' => 'Lead',

                // Traffic
                'utm_source' => $qs['utm_source'] ?? session('utm_source'),
                'utm_campaign' => $qs['utm_campaign'] ?? session('utm_campaign'),
                'utm_medium' => $qs['utm_medium'] ?? session('utm_medium'),
                'fbclid' => $qs['fbclid'] ?? session('meta_fbclid'),
                'gclid' => $qs['gclid'] ?? null,

                // Device & browser
                'device_id' => $deviceId,
                'session_hash' => $sessionHash,
                'fbp' => $fbp,
                'fbc' => $fbc,
                'browser_id' => $browserId,

                // Client Info
                'ip_address' => $ip,
                'user_agent' => $agent,
                'referer' => $referer,
                'landing_page' => $previousUrl,

                // Extra data
                'payload' => [
                    'button' => $buttonId,
                    'raw_device_id' => $rawDeviceId,
                ],
            ]);

            // CAPI EVENT SEND
            MetaCapiService::sendEvent('Lead', [
                'lead_id' => $lead->id,
                'type' => $type,
            ], $eventId);

        } catch (\Exception $e) {
            Log::error("Lead Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}
