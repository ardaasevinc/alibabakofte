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
     * WhatsApp Butonu
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
     * Ortak Lead Kayıt + CAPI Gönderimi
     */
    private function processLead($buttonId, $targetUrl)
{
    $previousUrl = url()->previous();

    $urlComponents = parse_url($previousUrl);
    parse_str($urlComponents['query'] ?? '', $urlQueries);

    $ip    = request()->ip();
    $agent = request()->userAgent();

    $type  = $buttonId === 'meta-whatsapp' ? 'whatsapp' : 'menu';

    // 1) Lead duplication engeli (24 saat)
    $exists = Lead::where('ip_address', $ip)
        ->where('user_agent', $agent)
        ->where('type', $type)
        ->where('created_at', '>', now()->subDay())
        ->exists();

    // Eğer zaten kaydı varsa:
    if ($exists) {
        // HİÇBİR ŞEKİLDE META CAPI EVENT GÖNDERİLMEYECEK!!!
        return redirect()->to($targetUrl);
    }

    // 2) Yeni lead oluşturulacaksa burası çalışır
    try {
        $lead = Lead::create([
            'type' => $type,
            'event_id' => 'evt_' . bin2hex(random_bytes(4)) . '_' . time(),
            'utm_source' => $urlQueries['utm_source'] ?? session('utm_source') ?? 'direct',
            'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign') ?? '-',
            'fbclid' => $urlQueries['fbclid'] ?? session('meta_fbclid'),
            'ip_address' => $ip,
            'user_agent' => $agent,
            'payload' => [
                'came_from' => $previousUrl,
                'button_id' => $buttonId,
                'utm_medium' => $urlQueries['utm_medium'] ?? session('utm_medium') ?? 'reklam',
            ],
        ]);

        // 3) CAPI EVENT SADECE BURADA GİDER (Yeni lead varsa!)
        MetaCapiService::sendEvent('Lead', [
            'lead_id' => $lead->id,
            'type'    => $type,
        ], $lead->event_id);

    } catch (\Exception $e) {
        Log::error("Lead Kayıt Hatası: " . $e->getMessage());
    }

    return redirect()->to($targetUrl);
}

}
