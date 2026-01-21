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
        $userAgent = request()->userAgent();

        // 1) BOT / SERVER-SIDE (Guzzle, curl, bot vs.) filtreleme
        if (
            ! $userAgent ||
            str_contains($userAgent, 'GuzzleHttp') ||
            str_contains($userAgent, 'curl') ||
            str_contains(strtolower($userAgent), 'bot') ||
            str_contains(strtolower($userAgent), 'spider')
        ) {
            return redirect()->to($targetUrl);
        }

        $ip   = request()->ip();
        $type = $buttonId === 'meta-whatsapp' ? 'whatsapp' : 'menu';

        // Device & session tracking
        $sessionHash = MetaCapiService::getSessionHash();
        $rawDeviceId = MetaCapiService::getOrCreateDeviceId();
        $deviceId    = hash('sha256', $rawDeviceId);
        $fbp         = MetaCapiService::getOrCreateFbp();
        $fbc         = MetaCapiService::getFormattedFbc();
        $browserId   = Cookie::get('browser_id');

        // Kaynak URL & UTM çözümleme
        $previousUrl = request()->headers->get('referer') ?? url()->previous() ?? url('/');
        $referer     = request()->headers->get('referer');
        $urlData     = parse_url($previousUrl);
        parse_str($urlData['query'] ?? '', $qs);

        // 24 saat içinde aynı device + aynı type lead limit
        $exists = Lead::where('type', $type)
            ->where('device_id', $deviceId)
            ->where('created_at', '>', now()->subHours(24))
            ->exists();

        if ($exists) {
            return redirect()->to($targetUrl);
        }

        try {
            $eventId = MetaCapiService::generateEventId();

            $lead = Lead::create([
                'type'          => $type,
                'event_id'      => $eventId,
                'event_name'    => 'Lead',

                'utm_source'    => $qs['utm_source'] ?? session('utm_source'),
                'utm_campaign'  => $qs['utm_campaign'] ?? session('utm_campaign'),
                'utm_medium'    => $qs['utm_medium'] ?? session('utm_medium'),
                'fbclid'        => $qs['fbclid'] ?? session('meta_fbclid'),
                'gclid'         => $qs['gclid'] ?? null,

                'device_id'     => $deviceId,
                'session_hash'  => $sessionHash,
                'fbp'           => $fbp,
                'fbc'           => $fbc,
                'browser_id'    => $browserId,

                'ip_address'    => $ip,
                'user_agent'    => $userAgent,
                'referer'       => $referer,
                'landing_page'  => $previousUrl,

                'payload' => [
                    'button'        => $buttonId,
                    'raw_device_id' => $rawDeviceId,
                ],
            ]);

            /**
             * CAPI EVENT GÖNDERİMİ
             * Ülke bilgisi: TR (sabit)
             */
            MetaCapiService::sendEvent('Lead', [
                'lead_id'   => $lead->id,
                'type'      => $type,

                // Advanced Matching için
                'device_id' => $deviceId,
                'fbp'       => $fbp,
                'fbc'       => $fbc,
                'country'   => 'tr',  // >>> EKLENDİ <<<
            ], $eventId);

        } catch (\Exception $e) {
            Log::error("Lead Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}
