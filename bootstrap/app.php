<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

       // Landing Katmanı
    $middleware->append(\App\Http\Middleware\CaptureLandingMiddleware::class);

    // PageView Katmanı (Lead ve Landing'den sonra çalışır)
    $middleware->append(\App\Http\Middleware\PageViewMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
