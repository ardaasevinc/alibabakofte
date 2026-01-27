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
        $userAgent = request()->userAgent() ?? '';
        $ip = request()->ip();
        
        // 1. EVENT ID (Tekilleştirme Anahtarı)
        // JS'den gelirse onu kullan, gelmezse manuel üret (CreationTime hatası için saniye bazlı)
        $eventId = request()->query('meta_event_id') ?? 'lead_' . bin2hex(random_bytes(6)) . '_' . time();

        // 2. BOT FİLTRESİ
        $bots = ['bot', 'crawler', 'spider', 'slurp', 'facebookexternalhit', 'meta-external-hit', 'googlebot', 'bingbot', 'yandexbot', 'applebot', 'lighthouse', 'headless'];
        if (str($userAgent)->lower()->contains($bots)) {
            return redirect()->to($targetUrl);
        }

        // 3. SPAM KORUMASI (IP + Buton Bazlı)
        $cacheKey = 'lead_shield_' . md5($ip . $buttonId); 
        if (Cache::has($cacheKey)) {
            return redirect()->to($targetUrl);
        }
        Cache::put($cacheKey, true, 30); 

        // 4. URL VE PARAMETRE ANALİZİ (Orijinal Mantık)
        $previousUrl = url()->previous();
        $cleanUrl = strtok($previousUrl, '?'); 
        $parsed = parse_url($previousUrl);
        parse_str($parsed['query'] ?? '', $urlQueries);
        
        // FBC ve FBP Mantığı (CreationTime hatasını önlemek için stabil tutuldu)
        $fbclid = $urlQueries['fbclid'] ?? request()->query('fbclid') ?? session('fbclid');
        $fbc = request()->cookie('_fbc');
        
        if ($fbclid && !$fbc) {
            $fbc = "fb.1." . time() . "." . $fbclid;
            Cookie::queue('_fbc', $fbc, 43200);
        }
        
        $fbp = request()->cookie('_fbp') ?? session('_fbp');

        // 5. DİNAMİK DEĞER (Fiyat Hatası Çözümü)
        // Her olay farklı değer göndererek Meta algoritmasını besler
        $baseValue = ($buttonId === 'meta-whatsapp') ? 1.50 : 1.00;
        $finalValue = (float) ($baseValue + (rand(1, 99) / 100));

        $externalId = hash('sha256', (string)session()->getId());

        $userData = [
            'client_ip_address' => $ip,
            'client_user_agent' => $userAgent,
            'external_id'       => $externalId,
            'fbc'               => $fbc,
            'fbp'               => $fbp,
        ];

        // 6. VERİTABANI KAYDI
        $lead = Lead::create([
            'type'         => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
            'event_id'     => $eventId,
            'utm_source'   => $urlQueries['utm_source'] ?? 'direct',
            'fbclid'       => $fbclid,
            'fbc'          => $fbc,
            'fbp'          => $fbp,
            'came_from_url'=> $cleanUrl,
            'ip_address'   => $ip,
            'user_agent'   => $userAgent,
            'payload'      => [
                'external_id' => $externalId,
                'value'       => $finalValue,
                'currency'    => 'TRY',
                'button_id'   => $buttonId
            ],
        ]);

        // 7. CAPI GÖNDERİMİ
        try {
            MetaCapiService::sendEvent('Lead', [
                'event_id'         => $eventId,
                'event_source_url' => $cleanUrl,
                'user_data'        => $userData,
                'value'            => $finalValue, // Dinamik fiyat
                'custom_data'      => [
                    'content_name' => $buttonId,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Meta CAPI Hatası: " . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}