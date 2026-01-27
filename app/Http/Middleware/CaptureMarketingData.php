<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CaptureMarketingData
{
    public function handle(Request $request, Closure $next)
    {
        // 1. UTM ve Meta Parametrelerini Yakala
        $params = ['utm_source', 'utm_medium', 'utm_campaign', 'fbclid', 'gclid'];

        foreach ($params as $param) {
            if ($request->has($param)) {
                $value = $request->query($param);
                session([$param => $value]);

                // fbclid gelmişse hemen fbc çerezi oluştur (CreationTime hatası için stabil başlangıç)
                if ($param === 'fbclid') {
                    $fbc = "fb.1." . time() . "." . $value;
                    Cookie::queue('_fbc', $fbc, 43200);
                }
            }
        }

        // 2. FBP Çerezi Yoksa Session'dan veya Yeniden Üreterek Kapsamı Artır
        if (!$request->cookie('_fbp') && !session()->has('_fbp')) {
            $fbp = "fb.1." . time() . "." . mt_rand(1000000000, 9999999999);
            session(['_fbp' => $fbp]);
            Cookie::queue('_fbp', $fbp, 43200);
        }

        return $next($request);
    }
}