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
        $phone = '905352855696';
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    public function menu(Request $request): RedirectResponse
    {
        return $this->processLead('meta-menu', url('/menu'));
    }

    private function processLead(string $buttonId, string $targetUrl): RedirectResponse
    {
        $userAgent = request()->userAgent();
        $ip = request()->ip();

        /* ============================================================
         * 1) GELİŞMİŞ BOT VE SPAM FİLTRESİ
         * ============================================================ */
        $bots = [
            'bot', 'crawler', 'spider', 'slurp', 'facebookexternalhit', 
            'meta-external-hit', 'googlebot', 'bingbot', 'yandexbot', 
            'applebot', 'lighthouse', 'headless'
        ];
        
        // Bot kontrolü
        if (str($userAgent)->lower()->contains($bots)) {
            return redirect()->to($targetUrl);
        }

        // Hız Limiti (Aynı IP'den 15 saniye içinde sadece 1 kayıt)
        $cacheKey = 'lead_lock_' . md5($ip); 
        if (Cache::has($cacheKey)) {
            return redirect()->to($targetUrl);
        }
        Cache::put($cacheKey, true, 15);

        /* ============================================================
         * 2) PARAMETRE VE URL ANALİZİ
         * ============================================================ */
        $previousUrl = url()->previous();
        $cleanUrl = strtok($previousUrl, '?'); 
        $parsed = parse_url($previousUrl);
        parse_str($parsed['query'] ?? '', $urlQueries);

        $fbclid = $urlQueries['fbclid'] ?? request()->query('fbclid') ?? session('fbclid');
        $eventId = request()->query('meta_event_id') ?? ('lead_' . str()->random(10) . '_' . time());

        /* ============================================================
         * 3) FBC / FBP VE USER DATA
         * ============================================================ */
        $fbc = $fbclid ? "fb.1." . time() . "." . $fbclid : request()->cookie('_fbc');
        $fbp = request()->cookie('_fbp') ?? ("fb.1." . time() . "." . mt_rand(1000000000, 9999999999));

        if ($fbclid) Cookie::queue('_fbc', $fbc, 43200);
        if (!request()->hasCookie('_fbp')) Cookie::queue('_fbp', $fbp, 43200);

        $userData = [
            'client_ip_address' => $ip,
            'client_user_agent' => $userAgent,
            'external_id'       => hash('sha256', (string)session()->getId()),
            'fbc'               => $fbc,
            'fbp'               => $fbp,
        ];

        if (Auth::check()) {
            $user = Auth::user();
            if ($user?->email) $userData['em'] = [hash('sha256', str($user->email)->trim()->lower())];
            if ($user?->phone) $userData['ph'] = [hash('sha256', preg_replace('/[^0-9]/', '', $user->phone))];
        }

        /* ============================================================
         * 4) VERİTABANI KAYDI
         * ============================================================ */
        $lead = Lead::create([
            'type'         => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
            'event_id'     => $eventId,
            'utm_source'   => $urlQueries['utm_source'] ?? 'direct',
            'utm_medium'   => $urlQueries['utm_medium'] ?? null,
            'utm_campaign' => $urlQueries['utm_campaign'] ?? null,
            'fbclid'       => $fbclid,
            'fbc'          => $fbc,
            'fbp'          => $fbp,
            'came_from_url'=> $cleanUrl,
            'ip_address'   => $ip,
            'user_agent'   => $userAgent,
            'platform'     => $this->detectPlatform(),
            'is_mobile'    => $this->isMobile(),
            'payload'      => ['button_id' => $buttonId],
        ]);

        /* ============================================================
         * 5) META CAPI GÖNDERİMİ
         * ============================================================ */
        try {
            MetaCapiService::sendEvent('Lead', [
                'event_id'         => $eventId,
                'event_source_url' => $cleanUrl,
                'user_data'        => $userData,
                'custom_data'      => [
                    'value'            => 1.00,
                    'currency'         => 'TRY',
                    'content_name'     => $buttonId,
                ]
            ], $eventId);
        } catch (\Exception $e) {
            Log::error("Meta CAPI Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }

    private function detectPlatform(): string
    {
        $ua = str(request()->userAgent())->lower();
        return $ua->contains(['iphone', 'ipad']) ? 'iOS' : ($ua->contains('android') ? 'Android' : 'Desktop');
    }

    private function isMobile(): bool
    {
        return (bool) preg_match('/Mobile|Android|iPhone|iPad/i', request()->userAgent());
    }
}