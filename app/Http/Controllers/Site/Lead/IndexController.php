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

        /**
         * 1) BOT / SERVER-SIDE İSTEKLERİ ELE
         * - GuzzleHttp (backend HTTP client)
         * - User-Agent içinde hiç tarayıcı izi olmayan istekler
         *
         * Bunlar için LEAD OLUŞTURMA, CAPI GÖNDERME.
         */
        if (
            str_contains($userAgent, 'GuzzleHttp')
            || (! str_contains($userAgent, 'Mozilla/') && ! str_contains($userAgent, 'Chrome/') && ! str_contains($userAgent, 'Safari/'))
        ) {
            return redirect()->to($targetUrl);
        }

        $ip   = request()->ip();
        $type = $buttonId === 'meta-whatsapp' ? 'whatsapp' : 'menu';

        // Device & session tracking
        $sessionId   = session()->getId();
        $sessionHash = hash('sha256', $sessionId);

        $rawDeviceId = MetaCapiService::getOrCreateDeviceId(); // cookie tabanlı UUID
        $deviceId    = hash('sha256', $rawDeviceId);           // DB & CAPI için hash

        $fbp       = MetaCapiService::getOrCreateFbp();
        $fbc       = MetaCapiService::getFormattedFbc();
        $browserId = Cookie::get('browser_id'); // JS ile atanıyor

        // URL kaynakları
        $previousUrl = request()->headers->get('referer') ?? url()->previous() ?? url('/');
        $referer     = request()->headers->get('referer');
        $urlData     = parse_url($previousUrl);
        parse_str($urlData['query'] ?? '', $qs);

        /**
         * 2) 24 SAAT İÇİNDE AYNI CİHAZ + AYNI TYPE İÇİN TEK LEAD
         *
         * device_id + type + 24h
         */
        $exists = Lead::where('type', $type)
            ->where('device_id', $deviceId)
            ->where('created_at', '>', now()->subHours(24))
            ->exists();

        if ($exists) {
            // ZATEN LEAD VAR → NE DB NE CAPI
            return redirect()->to($targetUrl);
        }

        // 3) YENİ LEAD OLUŞTUR
        try {
            $eventId = MetaCapiService::generateEventId();

            $lead = Lead::create([
                'type'          => $type,
                'event_id'      => $eventId,
                'event_name'    => 'Lead',

                // Traffic / UTM
                'utm_source'    => $qs['utm_source'] ?? session('utm_source'),
                'utm_campaign'  => $qs['utm_campaign'] ?? session('utm_campaign'),
                'utm_medium'    => $qs['utm_medium'] ?? session('utm_medium'),
                'fbclid'        => $qs['fbclid'] ?? session('meta_fbclid'),
                'gclid'         => $qs['gclid'] ?? null,

                // Matching data
                'device_id'     => $deviceId,
                'session_hash'  => $sessionHash,
                'fbp'           => $fbp,
                'fbc'           => $fbc,
                'browser_id'    => $browserId,

                // Client info
                'ip_address'    => $ip,
                'user_agent'    => $userAgent,
                'referer'       => $referer,
                'landing_page'  => $previousUrl,

                // Ek veriler
                'payload' => [
                    'button'        => $buttonId,    // meta-whatsapp / meta-menu
                    'raw_device_id' => $rawDeviceId, // hash'lenmemiş cihaz ID
                ],
            ]);

            // 4) META CAPI EVENT (Sadece yeni lead varsa)
            MetaCapiService::sendEvent('Lead', [
                'lead_id' => $lead->id,
                'type'    => $type,
            ], $eventId);

        } catch (\Exception $e) {
            Log::error("Lead Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}
