<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class MetaConversionStats extends BaseWidget
{
    protected static ?string $pollingInterval = '15s'; // canlı yenileme

    protected function getStats(): array
    {
        $today = Carbon::today();
        $weekAgo = Carbon::now()->subDays(7);
        $monthAgo = Carbon::now()->subDays(30);

        /* =====================================================================
         |  GÜN SONU DEĞERLERİ
         ===================================================================== */
        $todayWhatsApp = Lead::type('whatsapp')->whereDate('created_at', $today)->count();
        $todayMenu = Lead::type('menu')->whereDate('created_at', $today)->count();
        $todayAds = Lead::whereNotNull('fbclid')->whereDate('created_at', $today)->count();

        /* =====================================================================
         |  7 GÜNLÜK (WEEKLY) GRAFİK VERİSİ
         ===================================================================== */
        $weeklyWhatsApp = $this->buildDailyChart('whatsapp', $weekAgo);
        $weeklyMenu = $this->buildDailyChart('menu', $weekAgo);
        $weeklyAds = $this->buildDailyChartForAds($weekAgo);

        /* =====================================================================
         |  30 GÜNLÜK (MONTHLY) GRAFİK VERİSİ
         ===================================================================== */
        $monthlyWhatsApp = $this->buildDailyChart('whatsapp', $monthAgo);
        $monthlyMenu = $this->buildDailyChart('menu', $monthAgo);
        $monthlyAds = $this->buildDailyChartForAds($monthAgo);

        return [

            /* ============================================================
             |  WHATSAPP
             ============================================================ */
            Stat::make('WhatsApp Tıklamaları (Bugün)', $todayWhatsApp)
                ->description('7 Günlük & 30 Günlük Grafik Dahil')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('success')
                ->chart($weeklyWhatsApp), // 7 günlük çizgi
            Stat::make('WhatsApp 7 Günlük Trend', '')
                ->chart($weeklyWhatsApp)
                ->color('success')
                ->extraAttributes(['class' => 'border-t']),

            Stat::make('WhatsApp 30 Günlük Trend', '')
                ->chart($monthlyWhatsApp)
                ->color('success')
                ->extraAttributes(['class' => 'border-t']),

            /* ============================================================
             |  MENÜ
             ============================================================ */
            Stat::make('Menü Tıklamaları (Bugün)', $todayMenu)
                ->description('7 Günlük & 30 Günlük Grafik Dahil')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning')
                ->chart($weeklyMenu),
            Stat::make('Menü 7 Günlük Trend', '')
                ->chart($weeklyMenu)
                ->color('warning')
                ->extraAttributes(['class' => 'border-t']),

            Stat::make('Menü 30 Günlük Trend', '')
                ->chart($monthlyMenu)
                ->color('warning')
                ->extraAttributes(['class' => 'border-t']),

            /* ============================================================
             |  REKLAM TIKLAMALARI (FBCLID)
             ============================================================ */
            Stat::make('Reklam Tıklamaları (Bugün)', $todayAds)
                ->description('Meta Ads → fbclid ile gelenler')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info')
                ->chart($weeklyAds),
            Stat::make('Reklam 7 Günlük Trend', '')
                ->chart($weeklyAds)
                ->color('info')
                ->extraAttributes(['class' => 'border-t']),

            Stat::make('Reklam 30 Günlük Trend', '')
                ->chart($monthlyAds)
                ->color('info')
                ->extraAttributes(['class' => 'border-t']),
        ];
    }

    /* =====================================================================
     |  HELPER: BELLİ BİR TİP İÇİN GÜNLÜK GRUPLANMIŞ GRAFİK VERİSİ ÜRET
     ===================================================================== */
    private function buildDailyChart(string $type, Carbon $startDate): array
    {
        $records = Lead::type($type)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        return array_values($records);
    }

    private function buildDailyChartForAds(Carbon $startDate): array
    {
        $records = Lead::whereNotNull('fbclid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        return array_values($records);
    }
}
