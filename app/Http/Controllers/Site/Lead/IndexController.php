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
     * Ortak Kayıt ve Yönlendirme Mantığı
     */
    private function processLead($buttonId, $targetUrl)
    {
        // 1. Meta Deduplication (Tekilleştirme) için benzersiz ID
        $eventId = 'lead_' . bin2hex(random_bytes(4)) . '_' . time();
        $previousUrl = url()->previous();

        // 2. URL Parametrelerini Analiz Et
        $urlComponents = parse_url($previousUrl);
        parse_str($urlComponents['query'] ?? '', $urlQueries);

        $fbclid = $urlQueries['fbclid'] ?? session('fbclid') ?? request()->query('fbclid');

        try {
            // 3. Veritabanı Kaydı
            $lead = Lead::create([
                'type'         => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu', 
                'event_id'     => $eventId,
                'utm_source'   => $urlQueries['utm_source']   ?? session('utm_source') ?? 'direct',
                'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign') ?? '-',
                'fbclid'       => $fbclid,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'payload'      => [
                    'came_from'  => $previousUrl,
                    'button_id'  => $buttonId,
                    'utm_medium' => $urlQueries['utm_medium'] ?? session('utm_medium') ?? 'reklam',
                ],
            ]);

            // 4. Meta CAPI Gönderimi (Tazelenmiş Servis Çağrısı)
            MetaCapiService::sendEvent('Lead', [
                'external_id' => hash('sha256', (string) $lead->id),
                'event_source_url' => $previousUrl,
                // Eğer formda e-posta veya telefon alsaydık buraya hashleyip eklerdik
            ], $eventId);

        } catch (\Exception $e) {
            Log::error("alibaba Lead Kayıt Hatası: " . $e->getMessage());
        }

        // 5. Yönlendirme
        return redirect()->to($targetUrl);
    }
}