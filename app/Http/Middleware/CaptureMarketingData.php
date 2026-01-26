<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureMarketingData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   // app/Http/Middleware/CaptureMarketingData.php
public function handle(Request $request, Closure $next)
{
    // Meta ve UTM parametrelerini listele
    $params = ['utm_source', 'utm_medium', 'utm_campaign', 'fbclid'];

    foreach ($params as $param) {
        if ($request->has($param)) {
            session([$param => $request->query($param)]);
        }
    }

    return $next($request);
}
}
