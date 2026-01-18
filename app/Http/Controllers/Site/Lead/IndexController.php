<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\{Log, Cache, Session};
use Illuminate\Support\Str;

class IndexController extends Controller
{
    /**
     * WhatsApp yönlendirmesi ve Lead takibi.
     */
    public function whatsapp()
    {
        $phone = '905352855696';
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    /**
     * Menü yönlendirmesi ve Lead takibi.
     */
    public function menu()
    {
        return $this->processLead('meta-menu', route('site.menu.index'));
    }

    /**
     * Ortak Lead işleme mantığı.
     */
    private function processLead(string $buttonId, string $targetUrl)
    {
        $userAgent = request()->userAgent();

        // 1. GÜVENLİK: Bot ve Önizleme Araçlarını Engelleme
        if (
            request()->header('X-Purpose') == 'preview' || 
            str_contains(strtolower($userAgent), 'facebookexternalhit') ||
            str_contains(strtolower($userAgent), 'googlebot')
        ) {
            return redirect()->to($targetUrl);
        }

        // 2. GÜVENLİK: Mükerrer Tıklama Koruması (30 Saniye Kilit)
        $ipHash = md5(request()->ip() . $buttonId);
        $lockKey = 'lead_lock_' . $ipHash;
        $sessionKey = 'processed_' . $buttonId;

        if (Cache::has($lockKey) || Session::has($sessionKey)) {
            return redirect()->to($targetUrl);
        }

        // --- VERİ HAZIRLAMA ---
        $eventId = 'ab_' . Str::random(8) . '_' . time(); // Deduplication ID
        $previousUrl = url()->previous();
        
        // URL'deki query parametrelerini ayrıştır (utm_source, fbclid vb.)
        parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?? '', $urlQueries);

        try {
            // 3. VERİTABANI KAYDI
            $lead = Lead::create([
                'type' => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
                'event_id' => $eventId,
                'utm_source' => $urlQueries['utm_source'] ?? session('utm_source', 'direct'),
                'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign', '-'),
                'fbclid' => $urlQueries['fbclid'] ?? session('fbclid') ?? request()->cookie('_fbc'),
                'ip_address' => request()->ip(),
                'user_agent' => $userAgent,
                'payload' => [
                    'button_id' => $buttonId,
                    'referer'   => request()->headers->get('referer'),
                    'url'       => $previousUrl
                ],
            ]);

            // 4. META CAPI GÖNDERİMİ
            // UserData dizisi: Kişiyi tanımlayan veriler
            $userData = [
                'external_id' => (string) $lead->id,
                // Diğer bilgiler (em, ph) elimizde olsaydı buraya eklerdik
            ];

            // CustomData dizisi: Aksiyonu tanımlayan veriler
            $customData = [
                'content_name' => ($buttonId === 'meta-whatsapp') ? 'WhatsApp Lead' : 'Menu View',
                'content_type' => 'product',
                'value'        => 0.00, 
                'currency'     => 'TRY',
            ];

            MetaCapiService::sendEvent(
                'Lead', 
                $userData, 
                $customData, 
                $eventId
            );

            // 5. KİLİTLERİ AKTİF ET
            Session::put($sessionKey, time());
            Cache::put($lockKey, true, 30); // 30 saniye boyunca aynı IP'den yeni kayıt alma

        } catch (\Exception $e) {
            Log::error("Ali Baba Lead İşleme Hatası: " . $e->getMessage(), [
                'button' => $buttonId,
                'url' => $previousUrl
            ]);
        }

        return redirect()->to($targetUrl);
    }
}