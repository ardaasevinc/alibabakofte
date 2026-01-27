<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\{Cookie, Log, Auth, Cache};
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    public function whatsapp(Request $request): RedirectResponse
    {
        $phone = '+905352855696';
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    public function menu(Request $request): RedirectResponse
    {
        return $this->processLead('meta-menu', url('/menu'));
    }

    private function processLead(string $buttonId, string $targetUrl): RedirectResponse
    {
        $userAgent = request()->userAgent() ?? '';
        $ip = request()->ip();

        // 1. JS TARAFINDAN GELEN ID (Evrensel Tekilleştirme Anahtarı)
        $eventId = request()->query('meta_event_id');
        $isBotSuspect = false;

        if (!$eventId) {
            // JS çalışmadan (bot) gelen istekleri işaretle
            $eventId = 'lead_auto_' . str()->random(10) . '_' . time();
            $isBotSuspect = true;
        }

        /* ============================================================
         * 2) GELİŞMİŞ BOT VE SPAM FİLTRESİ (Laravel 12 Standartı)
         * ============================================================ */
        $bots = ['bot', 'crawler', 'spider', 'slurp', 'facebookexternalhit', 'meta-external-hit', 'googlebot', 'bingbot', 'yandexbot', 'applebot', 'lighthouse', 'headless', 'python', 'curl', 'wget'];

        // Görseldeki hatanın çözümü: str($userAgent)->lower() kullanımı
        if ($isBotSuspect || empty($userAgent) || str($userAgent)->lower()->contains($bots)) {
            return redirect()->to($targetUrl);
        }

        /* ============================================================
         * 3) IP TABANLI KİLİT (30 Saniye)
         * ============================================================ */
        $cacheKey = 'lead_shield_' . md5($ip);
        if (Cache::has($cacheKey)) {
            return redirect()->to($targetUrl);
        }
        Cache::put($cacheKey, true, 30);

        /* ============================================================
         * 4) EXTERNAL ID VE PARAMETRE ANALİZİ
         * ============================================================ */
        // Layout JS'deki init kısmıyla %100 aynı hash yöntemi
        $externalId = hash('sha256', (string) session()->getId());

        $previousUrl = url()->previous();
        $cleanUrl = strtok($previousUrl, '?');
        $parsed = parse_url($previousUrl);
        parse_str($parsed['query'] ?? '', $urlQueries);
        
        $fbclid = $urlQueries['fbclid'] ?? request()->query('fbclid') ?? session('fbclid');
        $fbc = $fbclid ? "fb.1." . time() . "." . $fbclid : request()->cookie('_fbc');
        $fbp = request()->cookie('_fbp') ?? ("fb.1." . time() . "." . mt_rand(1000000000, 9999999999));

        if ($fbclid)
            Cookie::queue('_fbc', $fbc, 43200);

        $userData = [
            'client_ip_address' => $ip,
            'client_user_agent' => $userAgent,
            'external_id'       => $externalId,
            'fbc'               => $fbc,
            'fbp'               => $fbp,
        ];

        /* ============================================================
         * 5) VERİTABANI KAYDI VE CAPI GÖNDERİMİ
         * ============================================================ */
        $lead = Lead::create([
            'type'         => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
            'event_id'     => $eventId,
            'utm_source' => $urlQueries['utm_source'] ?? 'direct',
            'fbclid'       => $fbclid,
            'fbc'          => $fbc,
            'fbp'          => $fbp,
            'came_from_url'=> $cleanUrl,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'payload' => ['external_id' => $externalId],
        ]);

        try {
            MetaCapiService::sendEvent('Lead', [
                'event_id' => $eventId,
                'event_source_url' => $cleanUrl,
                'user_data'        => $userData,
                'custom_data'      => [
                    'value' => 1.00,
                    'currency' => 'TRY',
                    'content_name' => $buttonId,
                ]
            ], $eventId);
        } catch (\Exception $e) {
            Log::error("Meta CAPI Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }

    private function detectPlatform(): string
    {
        $ua = str(request()->userAgent() ?? '')->lower();
        return $ua->contains(['iphone', 'ipad']) ? 'iOS' : ($ua->contains('android') ? 'Android' : 'Desktop');
    }

    private function isMobile(): bool
    {
        return (bool) preg_match('/Mobile|Android|iPhone|iPad/i', request()->userAgent() ?? '');
    }
}