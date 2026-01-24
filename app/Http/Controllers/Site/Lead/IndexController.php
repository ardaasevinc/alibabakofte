<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function whatsapp(Request $request)
    {
        return $this->processLead(
            'whatsapp',
            'meta-whatsapp',
            'https://wa.me/905352855696',
            $request
        );
    }

    public function menu(Request $request)
    {
        return $this->processLead(
            'menu',
            'meta-menu',
            route('site.menu.index'),
            $request
        );
    }

    private function processLead(
        string $type,
        string $buttonId,
        string $targetUrl,
        Request $request
    ) {
        $ua = $request->userAgent();

        // BOT filtresi
        if (
            empty($ua) ||
            str_contains($ua, 'GuzzleHttp') ||
            str_contains($ua, 'curl') ||
            str_contains(strtolower($ua), 'bot') ||
            str_contains(strtolower($ua), 'crawler') ||
            str_contains(strtolower($ua), 'spider')
        ) {
            return redirect()->to($targetUrl);
        }

        // Trafik verilerini yakala
        MetaCapiService::captureTrafficDataOnce($request);

        // Advanced Matching kimlikleri
        $externalId = MetaCapiService::getOrCreateExternalId($request);
        $sessionId  = session()->getId();
        $fbp        = MetaCapiService::getOrCreateFbp($request);
        $fbc        = MetaCapiService::getFormattedFbc($request);
        $deviceId   = MetaCapiService::getOrCreateDeviceId();
        $browserId  = MetaCapiService::getOrCreateBrowserId();
        $platform   = MetaCapiService::detectPlatform($ua);
        $isMobile   = MetaCapiService::isMobileDevice($ua);

        // QueryString
        $qs     = $request->query();
        $fbclid = $qs['fbclid'] ?? session('fbclid');
        $gclid  = $qs['gclid'] ?? null;

        // URL verileri
        $eventSourceUrl = strtok($request->fullUrl(), '?');
        $cameFrom       = url()->previous();
        $referer        = $request->headers->get('referer');

        try {
            // Event ID
            $eventId = MetaCapiService::generateEventId();

            // DB KAYIT
            $lead = Lead::create([
                'type'          => $type,
                'button_id'     => $buttonId,
                'event_id'      => $eventId,
                'event_name'    => 'Lead',

                'utm_source'    => session('utm_source'),
                'utm_medium'    => session('utm_medium'),
                'utm_campaign'  => session('utm_campaign'),
                'fbclid'        => $fbclid,
                'gclid'         => $gclid,

                'external_id'   => $externalId,
                'session_id'    => $sessionId,
                'device_id'     => $deviceId,
                'browser_id'    => $browserId,
                'fbp'           => $fbp,
                'fbc'           => $fbc,

                'ip_address'    => $request->ip(),
                'user_agent'    => $ua,
                'referer'       => $referer,
                'event_source_url' => $eventSourceUrl,
                'came_from_url'    => $cameFrom,

                'platform'      => $platform,
                'is_mobile'     => $isMobile,

                'payload'       => [
                    'full_query' => $qs,
                ],
            ]);

            // META CAPI EVENT
            MetaCapiService::sendEvent('Lead', [
                'type'      => $type,
                'lead_id'   => $lead->id,
                'button_id' => $buttonId,
            ], $eventId);

        } catch (\Throwable $e) {
            Log::error('Lead Error', ['error' => $e->getMessage()]);
        }

        return redirect()->to($targetUrl);
    }
}
