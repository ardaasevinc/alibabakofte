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
        $phone = '905352855696';
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
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
    private function processLead($buttonId, $targetUrl)
    {
        $previousUrl = url()->previous();

        // URL parametreleri
        $urlComponents = parse_url($previousUrl);
        parse_str($urlComponents['query'] ?? '', $urlQueries);

        $ip       = request()->ip();
        $agent    = request()->userAgent();
        $type     = ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu';
        $fbclid   = $urlQueries['fbclid'] ?? session('meta_fbclid');
        $utm_source   = $urlQueries['utm_source'] ?? session('utm_source') ?? 'direct';
        $utm_campaign = $urlQueries['utm_campaign'] ?? session('utm_campaign') ?? '-';
        $utm_medium   = $urlQueries['utm_medium'] ?? session('utm_medium') ?? 'reklam';

        /**
         * 1) SPAM / DOUBLE CLICK / REFRESH KONTROLÜ
         * Aynı kullanıcı (IP + Agent + Buton tipi) 24 saat içinde sadece 1 kayıt
         */
        $exists = Lead::where('ip_address', $ip)
            ->where('user_agent', $agent)
            ->where('type', $type)
            ->where('created_at', '>', now()->subDay())
            ->exists();

        if ($exists) {
            return redirect()->to($targetUrl);
        }

        /**
         * 2) LEAD KAYIT
         */
        try {
            $lead = Lead::create([
                'type' => $type,
                'event_id' => 'evt_' . bin2hex(random_bytes(4)) . '_' . time(),
                'utm_source' => $utm_source,
                'utm_campaign' => $utm_campaign,
                'fbclid' => $fbclid,
                'ip_address' => $ip,
                'user_agent' => $agent,
                'payload' => [
                    'came_from' => $previousUrl,
                    'button_id' => $buttonId,
                    'utm_medium' => $utm_medium,
                ],
            ]);

            /**
             * 3) META CAPI GÖNDERİMİ
             * Advanced matching verisi MetaCapiService içinde hazırlanıyor
             */
            MetaCapiService::sendEvent('Lead', [
                'lead_id' => $lead->id,
                'type'    => $type,
            ], $lead->event_id);

        } catch (\Exception $e) {
            Log::error("Lead Kayıt Hatası: " . $e->getMessage());
        }

        /**
         * 4) YÖNLENDİR
         */
        return redirect()->to($targetUrl);
    }
}
