<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    /**
     * WhatsApp Butonu
     */
    public function whatsapp(Request $request)
    {
        // type: whatsapp
        return $this->processLead(
            buttonId: 'meta-whatsapp',
            type: 'whatsapp',
            targetUrl: 'https://wa.me/905352855696'
        );
    }

    /**
     * Menü Butonu
     */
    public function menu(Request $request)
    {
        // type: menu
        return $this->processLead(
            buttonId: 'meta-menu',
            type: 'menu',
            targetUrl: route('site.menu.index')
        );
    }

    /**
     * Ortak Lead Kayıt + CAPI Gönderimi
     *
     * @param  string $buttonId   Pixel’de kullanacağın buton ID (meta-whatsapp, meta-menu vs.)
     * @param  string $type       Lead tablosundaki type alanı (whatsapp | menu vs.)
     * @param  string $targetUrl  Tıklama sonrası yönlendirilecek URL
     */
    private function processLead(string $buttonId, string $type, string $targetUrl)
    {
        $request   = request();
        $userAgent = $request->userAgent();

        // 0) Trafik verilerini (utm, fbclid, fbp) Session'a bir kere yaz
        MetaCapiService::captureTrafficDataOnce();

        // 1) BOT / SERVER-SIDE (Guzzle, curl, bot vs.) filtreleme
        if (
            ! $userAgent ||
            str_contains($userAgent, 'GuzzleHttp') ||
            str_contains($userAgent, 'curl') ||
            str_contains(strtolower($userAgent), 'bot') ||
            str_contains(strtolower($userAgent), 'spider') ||
            str_contains(strtolower($userAgent), 'crawler')
        ) {
            return redirect()->to($targetUrl);
        }

        // 2) IP, device, session bilgileri
        $ip          = $request->ip();
        $sessionHash = MetaCapiService::getSessionHash();
        $rawDeviceId = MetaCapiService::getOrCreateDeviceId();
        $deviceId    = hash('sha256', $rawDeviceId);
        $fbp         = MetaCapiService::getOrCreateFbp();
        $fbc         = MetaCapiService::getFormattedFbc();
        $browserId   = MetaCapiService::getOrCreateBrowserId();

        // 3) Kaynak URL & UTM çözümleme
        // Facebook reklamından gelindiğinde asıl parametreler şu anki request üzerinde.
        $landingPage = $request->fullUrl();
        $referer     = $request->headers->get('referer'); // çoğu zaman null (Facebook referrer policy)
        $qs          = $request->query();                 // tüm query parametreler

        // 4) 24 saat içinde aynı cihaz + aynı type için tekrar lead oluşturma
        // Test için kapatmak istersen: $exists = false;
        $exists = Lead::where('type', $type)
            ->where('device_id', $deviceId)
            ->where('created_at', '>', now()->subHours(24))
            ->exists();

        if ($exists) {
            return redirect()->to($targetUrl);
        }

        try {
            $eventId = MetaCapiService::generateEventId();

            // 5) Lead kaydı
            $lead = Lead::create([
                'type'          => $type,
                'event_id'      => $eventId,
                'event_name'    => 'Lead',

                'utm_source'    => $qs['utm_source']   ?? session('utm_source'),
                'utm_campaign'  => $qs['utm_campaign'] ?? session('utm_campaign'),
                'utm_medium'    => $qs['utm_medium']   ?? session('utm_medium'),
                'fbclid'        => $qs['fbclid']       ?? session('meta_fbclid'),
                'gclid'         => $qs['gclid']        ?? null,

                'device_id'     => $deviceId,
                'session_hash'  => $sessionHash,
                'fbp'           => $fbp,
                'fbc'           => $fbc,
                'browser_id'    => $browserId,

                'ip_address'    => $ip,
                'user_agent'    => $userAgent,
                'referer'       => $referer,
                'landing_page'  => $landingPage,

                'payload'       => [
                    'button'        => $buttonId,
                    'raw_device_id' => $rawDeviceId,
                ],
            ]);

            // 6) Meta CAPI Event (Advanced Matching ile)
            MetaCapiService::sendEvent('Lead', [
                'lead_id'   => $lead->id,
                'type'      => $type,
                'device_id' => $deviceId,
                'fbp'       => $fbp,
                'fbc'       => $fbc,
                'country'   => 'tr',
            ], $eventId);

        } catch (\Throwable $e) {
            Log::error('Lead Hatası: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return redirect()->to($targetUrl);
    }
}
