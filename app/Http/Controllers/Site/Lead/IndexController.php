<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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

    private function processLead($buttonId, $targetUrl)
    {
        // 1. GÜVENLİK: Bot ve Prefetch Engelleme
        $userAgent = request()->userAgent();
        if (request()->header('X-Purpose') == 'preview' || str_contains($userAgent, 'facebookexternalhit')) {
            return redirect()->to($targetUrl);
        }

        // 2. GÜVENLİK: Referer Kontrolü (Sadece sitemizden gelen tıklamayı kabul et)
        // Eğer kullanıcı URL'yi kopyalayıp yapıştırırsa veya F5 yaparsa bu blok çalışmaz.
        $referer = request()->headers->get('referer');
        if (!$referer || !str_contains($referer, request()->getHost())) {
            return redirect()->to($targetUrl);
        }

        // 3. GÜVENLİK: Session Kilidi (Sayfa Yenileme Engeli)
        // Kullanıcı bir kez tıkladığında 30 saniye boyunca aynı buton için yeni kayıt oluşturmaz.
        $sessionKey = 'processed_' . $buttonId . '_' . md5($referer);
        if (session()->has($sessionKey)) {
            return redirect()->to($targetUrl);
        }

        // 4. GÜVENLİK: Mükerrer Tıklama Kilidi (IP Tabanlı Cache)
        $lockKey = 'lead_lock_' . md5(request()->ip() . $buttonId);
        if (Cache::has($lockKey)) {
            return redirect()->to($targetUrl);
        }

        // --- BURADAN SONRASI SADECE GERÇEK TIKLAMADA ÇALIŞIR ---
        
        $eventId = 'ab_' . uniqid() . '_' . time();
        $previousUrl = url()->previous();
        parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?? '', $urlQueries);

        try {
            // VERİTABANI KAYDI
            $lead = Lead::create([
                'type' => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
                'event_id' => $eventId,
                'utm_source' => $urlQueries['utm_source'] ?? session('utm_source', 'direct'),
                'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign', '-'),
                'fbclid' => $urlQueries['fbclid'] ?? session('fbclid'),
                'ip_address' => request()->ip(),
                'user_agent' => $userAgent,
                'payload' => [
                    'came_from' => $previousUrl,
                    'button_id' => $buttonId,
                ],
            ]);

            // META CAPI GÖNDERİMİ
            MetaCapiService::sendEvent(
                'Lead', 
                [
                    'external_id' => hash('sha256', (string) $lead->id),
                    'fbc' => $lead->fbclid ? "fb.1." . time() . "." . $lead->fbclid : null,
                    'event_source_url' => $previousUrl,
                ], 
                $eventId, 
                null // Test bittiği için null
            );

            // İşlem başarılı, session ve cache kilitlerini koy
            session()->put($sessionKey, time());
            Cache::put($lockKey, true, 30);

        } catch (\Exception $e) {
            Log::error("Ali Baba Lead Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}