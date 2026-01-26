<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class IndexController extends Controller
{
    public function whatsapp(Request $request): RedirectResponse
    {
        return $this->processLead($request, 'whatsapp', 'meta-whatsapp', 'https://wa.me/905352855696');
    }

    public function menu(Request $request): RedirectResponse
    {
        return $this->processLead($request, 'menu', 'meta-menu', route('site.menu.index'));
    }

    private function processLead(Request $request, string $type, string $buttonId, string $targetUrl): RedirectResponse
    {
        $ua = $request->userAgent() ?? '';

        // Bot Filtresi
        if (empty($ua) || preg_match('/bot|crawl|slurp|spider|guzzle|curl/i', $ua)) {
            return redirect()->to($targetUrl);
        }

        // Veri Yakalama
        MetaCapiService::captureTrafficDataOnce($request);
        $eventId = MetaCapiService::generateEventId();

        try {
            // Veritabanı Kaydı (Tüm detaylarla)
            $lead = Lead::create([
                'type' => $type,
                'button_id' => $buttonId,
                'event_id' => $eventId,
                'event_name' => 'Lead',
                'utm_source' => session('utm_source'),
                'utm_medium' => session('utm_medium'),
                'utm_campaign' => session('utm_campaign'),
                'utm_term' => session('utm_term'),
                'utm_content' => session('utm_content'),
                'fbclid' => $request->query('fbclid') ?? session('fbclid'),
                'external_id' => MetaCapiService::getOrCreateExternalId($request),
                'session_id' => session()->getId(),
                'device_id' => MetaCapiService::getOrCreateDeviceId(),
                'browser_id' => MetaCapiService::getOrCreateBrowserId(),
                'fbp' => MetaCapiService::getOrCreateFbp($request),
                'fbc' => MetaCapiService::getFormattedFbc($request),
                'ip_address' => $request->ip(),
                'user_agent' => $ua,
                'referer' => $request->headers->get('referer'),
                'event_source_url' => strtok($request->fullUrl(), '?'),
                'came_from_url' => url()->previous(),
                'platform' => MetaCapiService::detectPlatform($ua),
                'is_mobile' => MetaCapiService::isMobileDevice($ua),
                'payload' => ['full_query' => $request->query()],
            ]);

            // CAPI Gönderimi
            MetaCapiService::sendEvent(
                eventName: 'Lead',
                customData: [
                    'type' => $type,
                    'lead_id' => (string) $lead->id,
                    'button_id' => $buttonId,
                ],
                eventId: $eventId
            );
        } catch (\Throwable $e) {
            Log::error('Lead Error: ' . $e->getMessage());
        }

        return redirect()->to($targetUrl);
    }
}