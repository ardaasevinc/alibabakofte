<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    /**
     * WhatsApp butonuna tıklandığında tetiklenir.
     */
    public function whatsapp(Request $request): RedirectResponse
    {
        // Ali Baba numaranı buraya yaz
        $phone = '905352855696';

        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    /**
     * Menü butonuna tıklandığında tetiklenir.
     */
    public function menu(Request $request): RedirectResponse
    {
        return $this->processLead('meta-menu', url('/menu'));
    }

    /**
     * Lead sürecini yöneten ana fonksiyon.
     */
    private function processLead(string $buttonId, string $targetUrl): RedirectResponse
    {
        $request   = request();
        $userAgent = $request->userAgent() ?? '';
        $ip        = $request->ip();

        /* ============================================================
         * 0) BOT FİLTRESİ
         * ============================================================ */
        if ($this->containsBot($userAgent)) {
            Log::info('LEAD BOT ENGELLENDİ', [
                'ip'         => $ip,
                'user_agent' => $userAgent,
                'button_id'  => $buttonId,
            ]);

            return redirect()->to($targetUrl);
        }

        /* ============================================================
         * 0.5) IP LOCK (30 saniye)
         * ============================================================ */
        $cacheKey = 'lead_lock_' . $buttonId . '_' . md5($ip);

        if (Cache::has($cacheKey)) {
            Log::info('LEAD IP LOCK ENGELLENDİ', [
                'ip'        => $ip,
                'button_id' => $buttonId,
            ]);

            // Kayıt yazmadan, CAPI göndermeden direkt yönlendir
            return redirect()->to($targetUrl);
        }

        // 30 saniyelik kilit
        Cache::put($cacheKey, true, 30);

        /* ============================================================
         * 1) Deduplication (Tekilleştirme) ID Üretimi
         * ============================================================ */
        $eventId = $request->query('meta_event_id')
            ?? $request->cookie('meta_event_id')
            ?? ('lead_' . bin2hex(random_bytes(6)) . '_' . time());

        Cookie::queue('meta_event_id', $eventId, 10);

        /* ============================================================
         * 2) URL ve Parametre Analizi
         * ============================================================ */
        // Öncelik referer (özellikle reklam & sosyal medya için)
        $previousUrl = $request->headers->get('referer')
            ?? url()->previous()
            ?? '';

        $cleanUrl = $previousUrl ? strtok($previousUrl, '?') : null;

        $parsed = parse_url($previousUrl);
        parse_str($parsed['query'] ?? '', $urlQueries);

        $fbclid = $urlQueries['fbclid']
            ?? $request->query('fbclid')
            ?? session('fbclid');

        /* ============================================================
         * 3) fbc ve fbp Tanımlayıcıları
         * ============================================================ */
        $fbc = null;
        if ($fbclid) {
            $fbc = 'fb.1.' . time() . '.' . $fbclid;
            Cookie::queue('_fbc', $fbc, 43200); // 30 gün
        }

        $fbp = $request->cookie('_fbp');
        if (!$fbp) {
            $fbp = 'fb.1.' . time() . '.' . mt_rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 43200); // 30 gün
        }

        /* ============================================================
         * 4) Gelişmiş Eşleştirme (Advanced Matching) Verileri
         * ============================================================ */
        $externalId = hash('sha256', (string) session()->getId());

        $userData = [
            'client_ip_address' => $ip,
            'client_user_agent' => $userAgent,
            'external_id'       => $externalId,
            'fbc'               => $fbc,
            'fbp'               => $fbp,
        ];

        if (Auth::check()) {
            $user = Auth::user();

            if ($user?->email) {
                $hashedEmail = hash(
                    'sha256',
                    str($user->email)->trim()->lower()->toString()
                );
                $userData['em'] = [$hashedEmail];
            }

            if ($user?->phone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);
                if ($cleanPhone) {
                    $userData['ph'] = [hash('sha256', (string) $cleanPhone)];
                }
            }
        }

        /* ============================================================
         * 5) Lead Kaydı
         * ============================================================ */
        $lead = Lead::create([
            'type'         => $buttonId === 'meta-whatsapp' ? 'whatsapp' : 'menu',
            'event_id'     => $eventId,
            'utm_source'   => $urlQueries['utm_source']   ?? 'direct',
            'utm_medium'   => $urlQueries['utm_medium']   ?? null,
            'utm_campaign' => $urlQueries['utm_campaign'] ?? null,
            'fbclid'       => $fbclid,
            'fbc'          => $fbc,
            'fbp'          => $fbp,
            'came_from_url'=> $cleanUrl,
            'ip_address'   => $ip,
            'user_agent'   => $userAgent,
            'platform'     => $this->detectPlatform(),
            'is_mobile'    => $this->isMobile(),
            'payload'      => [
                'button_id'   => $buttonId,
                'external_id' => $externalId,
            ],
        ]);

        Log::info('LEAD KAYDEDİLDİ', [
            'lead_id'    => $lead->id,
            'button_id'  => $buttonId,
            'came_from'  => $cleanUrl,
            'ip'         => $ip,
            'user_agent' => $userAgent,
        ]);

        /* ============================================================
         * 6) Meta CAPI Gönderimi
         * ============================================================ */
        try {
            MetaCapiService::sendEvent('Lead', [
                'event_id'         => $eventId,
                'event_time'       => time(),
                'event_source_url' => $cleanUrl,
                'action_source'    => 'website',
                'user_data'        => $userData,
                'custom_data'      => [
                    'value'            => 1.00,
                    'currency'         => 'TRY',
                    'content_name'     => $buttonId,
                    'content_category' => 'Lead Generation',
                    'lead_id'          => (string) $lead->id,
                ],
            ], $eventId);
        } catch (\Exception $e) {
            Log::error('Meta CAPI Hatası', [
                'message'   => $e->getMessage(),
                'button_id' => $buttonId,
                'lead_id'   => $lead->id ?? null,
            ]);
        }

        return redirect()->to($targetUrl);
    }

    /**
     * BOT Algılama – User Agent tabanlı
     */
    private function containsBot(string $ua): bool
    {
        $ua = strtolower($ua);

        $bots = [
            'bot',
            'crawler',
            'spider',
            'slurp',
            'facebookexternalhit',
            'meta-external-hit',
            'googlebot',
            'bingbot',
            'yandexbot',
            'applebot',
            'lighthouse',
            'headless',
            'python',
            'curl',
            'wget',
            'ahrefs',
            'semrush',
            'mj12',
            'linkdex',
        ];

        foreach ($bots as $bot) {
            if (str_contains($ua, $bot)) {
                return true;
            }
        }

        return false;
    }

    private function detectPlatform(): string
    {
        $ua = str(request()->userAgent() ?? '')->lower();

        if ($ua->contains(['iphone', 'ipad', 'ios'])) {
            return 'iOS';
        }

        if ($ua->contains('android')) {
            return 'Android';
        }

        return 'Desktop';
    }

    private function isMobile(): bool
    {
        return (bool) preg_match(
            '/Mobile|Android|iPhone|iPad/i',
            request()->userAgent() ?? ''
        );
    }
}
