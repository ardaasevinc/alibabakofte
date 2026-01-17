<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            
            // --- Marka ve Logo Ayarları ---
            ->brandName('Ali Baba Köfte')
            ->brandLogo(asset('site/alibaba/logos/logo.svg')) // Açık tema logosu
            ->darkModeBrandLogo(asset('site/alibaba/logos/logo-white.svg')) // Koyu tema logosu
            ->brandLogoHeight('3rem')
            ->favicon(asset('site/alibaba/logos/favicon.svg'))
            
            // --- Navigasyon ve Sidebar Ayarları ---
            ->sidebarCollapsible() // Sidebar'ı tamamen daraltılabilir yapar
            ->navigationGroups([
                'Sipariş Yönetimi',
                'İçerik Yönetimi',
                'Sistem Ayarları',
            ])
            
            // --- Renk Paleti (Amber/Turuncu Köfte Temasına Uygun) ---
            ->colors([
                'primary' => Color::Amber,
            ])
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}