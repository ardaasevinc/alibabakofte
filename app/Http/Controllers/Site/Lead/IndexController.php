<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // IDE hatasını çözen Facade
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    /**
     * WhatsApp butonuna tıklandığında tetiklenir.
     */
    public function whatsapp(Request $request): RedirectResponse
    {
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
        /* ============================================================
         * 1) Deduplication (Tekilleştirme) ID Üretimi
         * ============================================================ */
        $eventId = request()->query('meta_event_id') ?? ('lead_' . bin2hex(random_bytes(6)) . '_' . time());
        Cookie::queue('meta_event_id', $eventId, 10);

        /* ============================================================
         * 2) URL ve Parametre Analizi
         * ============================================================ */
        $previousUrl = url()->previous();
        $cleanUrl = strtok($previousUrl, '?'); 

        $parsed = parse_url($previousUrl);
        parse_str($parsed['query'] ?? '', $urlQueries);

        $fbclid = $urlQueries['fbclid'] ?? request()->query('fbclid') ?? session('fbclid');

        /* ============================================================
         * 3) fbc ve fbp Tanımlayıcıları
         * ============================================================ */
        $fbc = null;
        if ($fbclid) {
            $fbc = "fb.1." . time() . "." . $fbclid;
            Cookie::queue('_fbc', $fbc, 43200);
        }

        $fbp = request()->cookie('_fbp');
        if (!$fbp) {
            $fbp = "fb.1." . time() . "." . mt_rand(1000000000, 9999999999);
            Cookie::queue('_fbp', $fbp, 43200);
        }

        /* ============================================================
         * 4) Gelişmiş Eşleştirme (Advanced Matching) Verileri
         * ============================================================ */
        $externalId = hash('sha256', (string)session()->getId());
        
        $userData = [
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
            'external_id'       => $externalId,
            'fbc'               => $fbc,
            'fbp'               => $fbp,
        ];

        // IDE Hatanızın Çözümü: Auth Facade ve Null-safe operator kullanımı
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user?->email) {
                // Laravel 12 modern string manipülasyonu
                $hashedEmail = hash('sha256', str($user->email)->trim()->lower()->toString());
                $userData['em'] = [$hashedEmail];
            }

            if ($user?->phone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);
                $userData['ph'] = [hash('sha256', (string)$cleanPhone)];
            }
        }

        /* ============================================================
         * 5) Lead Kaydı ve CAPI Gönderimi
         * ============================================================ */
        $lead = Lead::create([
            'type'         => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
            'event_id'     => $eventId,
            'utm_source'   => $urlQueries['utm_source']   ?? 'direct',
            'utm_medium'   => $urlQueries['utm_medium']   ?? null,
            'utm_campaign' => $urlQueries['utm_campaign'] ?? null,
            'fbclid'       => $fbclid,
            'fbc'          => $fbc,
            'fbp'          => $fbp,
            'came_from_url'=> $cleanUrl,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'platform'     => $this->detectPlatform(),
            'is_mobile'    => $this->isMobile(),
            'payload'      => [
                'button_id'   => $buttonId,
                'external_id' => $externalId,
            ],
        ]);

        try {
            MetaCapiService::sendEvent('Lead', [
                'event_id'         => $eventId,
                'event_time'       => time(),
                'event_source_url' => $cleanUrl,
                'action_source'    => 'website',
                'user_data'        => $userData,
                'custom_data'      => [
                    'value'            => 1.00, // Paneldeki "Geçerli Değer" hatası çözüldü
                    'currency'         => 'TRY', // Paneldeki "Para Birimi" hatası çözüldü
                    'content_name'     => $buttonId,
                    'content_category' => 'Lead Generation',
                    'lead_id'          => (string)$lead->id,
                ]
            ], $eventId);
        } catch (\Exception $e) {
            Log::error("Peçka Meta CAPI Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }

    private function detectPlatform(): string
    {
        $ua = str(request()->userAgent())->lower();
        if ($ua->contains(['iphone', 'ipad', 'ios'])) return 'iOS';
        if ($ua->contains('android')) return 'Android';
        return 'Desktop';
    }

    private function isMobile(): bool
    {
        return (bool) preg_match('/Mobile|Android|iPhone|iPad/i', request()->userAgent());
    }
}