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
        $phone = '905331959477'; 
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    public function menu()
    {
        return $this->processLead('meta-menu', route('site.menu.index'));
    }

    private function processLead($buttonId, $targetUrl)
    {
        // 1. GÜVENLİK: Botları ve Tarayıcı Ön Yüklemelerini (Prefetch) Engelle
        if (request()->header('X-Purpose') == 'preview' || request()->header('X-Moz') == 'prefetch' || request()->header('Purpose') == 'prefetch') {
            return redirect()->to($targetUrl);
        }

        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        // 2. GÜVENLİK: Eğer kullanıcı sayfayı yeniliyorsa veya referer yoksa kayıt atma
        // (Sadece başka bir sayfadan gelen tıklamaları kabul eder)
        if ($previousUrl === $currentUrl || empty($previousUrl)) {
            return redirect()->to($targetUrl);
        }

        // 3. GÜVENLİK: Mükerrer Tıklama Kilidi (Aynı kişi 10 saniye içinde tekrar kayıt atamaz)
        $lockKey = 'lead_lock_' . md5(request()->ip() . $buttonId);
        if (Cache::has($lockKey)) {
            return redirect()->to($targetUrl);
        }
        Cache::put($lockKey, true, 10);

        $eventId = 'ab_' . uniqid() . '_' . time();
        parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?? '', $urlQueries);

        try {
            $lead = Lead::create([
                'type' => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
                'event_id' => $eventId,
                'utm_source' => $urlQueries['utm_source'] ?? session('utm_source', 'direct'),
                'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign', '-'),
                'fbclid' => $urlQueries['fbclid'] ?? session('fbclid'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'payload' => [
                    'came_from' => $previousUrl,
                    'button_id' => $buttonId,
                    'utm_medium' => $urlQueries['utm_medium'] ?? 'reklam',
                ],
            ]);

            MetaCapiService::sendEvent('Lead', [
                'external_id' => hash('sha256', (string) $lead->id),
                'fbc' => $lead->fbclid ? "fb.1." . time() . "." . $lead->fbclid : null,
                'event_source_url' => $previousUrl,
            ], $eventId);

        } catch (\Exception $e) {
            Log::error("Ali Baba Lead Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}