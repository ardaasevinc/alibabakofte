<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\MetaCapiService;

class CaptureLandingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Tek seferlik event kontrolü
        if (!Session::has('landing_sent')) {

            $referer = $request->headers->get('referer');
            
            // UTM'leri alalım
            $utm = [
                'utm_source'   => $request->query('utm_source'),
                'utm_medium'   => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
            ];

            // Meta event payload
            $payload = [
                'landing_url' => $request->fullUrl(),
                'referer'     => $referer,
                'utm'         => $utm,
            ];

            // CAPI gönder
            MetaCapiService::sendEvent('Landing', $payload);

            // Tekrar gönderimi engelle
            Session::put('landing_sent', true);
        }

        return $next($request);
    }
}
