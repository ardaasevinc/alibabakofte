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
     * Direkt WhatsApp Butonu (Sipariş/İletişim)
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
     * Ortak Kayıt ve Yönlendirme Mantığı (Spam Kontrollü)
     */
    private function processLead($buttonId, $targetUrl)
    {
        $eventId = 'lead_' . bin2hex(random_bytes(4)) . '_' . time();
        $previousUrl = url()->previous();

        // URL parametrelerini çöz
        $urlComponents = parse_url($previousUrl);
        parse_str($urlComponents['query'] ?? '', $urlQueries);

        $fbclid = $urlQueries['fbclid'] ?? session('fbclid') ?? request()->query('fbclid');

        // Kullanıcı bilgileri
        $ip = request()->ip();
        $agent = request()->userAgent();
        $type = ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu';

        /* ============================================================
         * 1) SPAM / DOUBLE CLICK / REFRESH / BACK-FORWARD ENGELİ
         * Son 24 saatte aynı (ip + agent + type) varsa kayıt oluşturulmaz.
         * ============================================================ */
        $exists = Lead::where('ip_address', $ip)
            ->where('user_agent', $agent)
            ->where('type', $type)
            ->where('created_at', '>', now()->subDay())
            ->exists();

        if ($exists) {
            return redirect()->to($targetUrl);
        }

        /* ============================================================
         * 2) KAYIT OLUŞTUR
         * ============================================================ */
        try {
            $lead = Lead::create([
                'type'         => $type,
                'event_id'     => $eventId,
                'utm_source'   => $urlQueries['utm_source']   ?? session('utm_source') ?? 'direct',
                'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign') ?? '-',
                'fbclid'       => $fbclid,
                'ip_address'   => $ip,
                'user_agent'   => $agent,
                'payload'      => [
                    'came_from'  => $previousUrl,
                    'button_id'  => $buttonId,
                    'utm_medium' => $urlQueries['utm_medium'] ?? session('utm_medium') ?? 'reklam',
                ],
            ]);

            /* ============================================================
             * 3) META CAPI EVENT
             * ============================================================ */
            MetaCapiService::sendEvent('Lead', [
                'external_id'       => hash('sha256', (string) $lead->id),
                'event_source_url'  => $previousUrl,
            ], $eventId);

        } catch (\Exception $e) {
            Log::error("alibaba Lead Kayıt Hatası: " . $e->getMessage());
        }

        /* ============================================================
         * 4) YÖNLENDİR
         * ============================================================ */
        return redirect()->to($targetUrl);
    }
}
