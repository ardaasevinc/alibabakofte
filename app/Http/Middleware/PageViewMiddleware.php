<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\MetaCapiService;

class PageViewMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // 1) BOT / SERVER İSTEKLERİNİ ELE
        $agent = $request->userAgent();
        if (
            ! $agent ||
            str_contains($agent, 'GuzzleHttp') ||
            str_contains($agent, 'curl') ||
            str_contains(strtolower($agent), 'bot') ||
            str_contains(strtolower($agent), 'spider') ||
            str_contains(strtolower($agent), 'crawler')
        ) {
            return $response;
        }

        // 2) Aynı sayfa için aynı oturumda tekrar gönderme
        $pageKey = 'pageview_' . md5($request->fullUrl());
        if (! Session::has($pageKey)) {
            Session::put($pageKey, true);

            MetaCapiService::sendEvent('PageView', [
                'url' => $request->fullUrl(),
            ]);
        }

        return $response;
    }
}
