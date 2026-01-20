<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\{Log, Cache, Session};
use Illuminate\Support\Str;

class IndexController extends Controller
{
    public function whatsapp()
    {
        $phone = '905352855696';
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    public function menu()
    {
        return $this->processLead('meta-menu', route('site.menu.index'));
    }

    private function processLead(string $buttonId, string $targetUrl)
    {
        $userAgent = request()->userAgent();

        // 1. GÜVENLİK: Bot Engelleme
        if ($this->isBot($userAgent)) {
            return redirect()->to($targetUrl);
        }

        // 2. MÜKERRER TIKLAMA KORUMASI
        $ipHash = md5(request()->ip() . $buttonId);
        $lockKey = 'lead_lock_' . $ipHash;
        if (Cache::has($lockKey)) {
            return redirect()->to($targetUrl);
        }

        // --- VERİ HAZIRLAMA ---
        // MetaCapiService'in yeni yapısını kullanarak trafik verilerini session'a işle
        MetaCapiService::captureTrafficData();

        $eventId = 'ab_' . Str::random(8) . '_' . time();

        try {
            // 3. VERİTABANI KAYDI (Kritik Alan: Kaynak Tespiti)
            $lead = Lead::create([
                'type'         => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
                'event_id'     => $eventId,
                // Session'da varsa al, yoksa 'direct' yaz
                'utm_source'   => session('utm_source', MetaCapiService::getDetectedSource()), 
                'utm_campaign' => session('utm_campaign', '-'),
                'fbclid'       => session('meta_fbclid') ?? request()->cookie('_fbc'),
                'ip_address'   => request()->ip(),
                'user_agent'   => $userAgent,
                'payload'      => [
                    'button_id' => $buttonId,
                    'referer'   => request()->headers->get('referer'),
                    'session_id'=> session()->getId()
                ],
            ]);

            // 4. META CAPI GÖNDERİMİ
            // customData içinde aksiyon detaylarını gönderiyoruz
            $customData = [
                'content_name' => ($buttonId === 'meta-whatsapp') ? 'WhatsApp Lead' : 'Menu View',
                'button_id'    => $buttonId,
                'value'        => 0.00, 
                'currency'     => 'TRY',
            ];

            // Service içindeki sendEvent metodunu çağırıyoruz
            MetaCapiService::sendEvent('Lead', $customData, $eventId);

            // 5. KİLİDİ AKTİF ET
            Cache::put($lockKey, true, 30); 

        } catch (\Exception $e) {
            Log::error("Lead İşleme Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }

    private function isBot($userAgent): bool
    {
        return request()->header('X-Purpose') == 'preview' || 
               str_contains(strtolower($userAgent), 'facebookexternalhit') ||
               str_contains(strtolower($userAgent), 'googlebot') ||
               str_contains(strtolower($userAgent), 'twitterbot');
    }
}