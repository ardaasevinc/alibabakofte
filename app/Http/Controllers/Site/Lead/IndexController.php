<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function whatsapp()
    {
        $phone = '905331959477'; // Ali Baba Köfte İletişim Hattı
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    public function menu()
    {
        return $this->processLead('meta-menu', route('site.menu.index'));
    }

    private function processLead($buttonId, $targetUrl)
    {
        $eventId = 'ab_' . uniqid() . '_' . time();
        $previousUrl = url()->previous();
        
        // URL'den UTM parametrelerini ayıkla
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