<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MetaConversionStats extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = Carbon::today();

        // 1. WhatsApp Trend Verisi (Son 7 Gün)
        $whatsappTrend = Lead::where('type', 'whatsapp')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->groupBy('date')
            ->pluck('aggregate')
            ->toArray();

        // 2. Reklam Trend Verisi (Son 7 Gün)
        $adsTrend = Lead::whereNotNull('fbclid')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->groupBy('date')
            ->pluck('aggregate')
            ->toArray();

        // 3. Mobil Oranı Hesaplama
        $totalToday = Lead::whereDate('created_at', $today)->count();
        $mobileToday = Lead::whereDate('created_at', $today)->where('is_mobile', true)->count();
        $mobilePercentage = $totalToday > 0 ? round(($mobileToday / $totalToday) * 100) : 0;

        return [
            // WhatsApp Dönüşümleri
            Stat::make('WhatsApp Talepleri', Lead::where('type', 'whatsapp')->whereDate('created_at', $today)->count())
                ->description('Bugün gelen canlı iletişim')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($whatsappTrend ?: [0, 0])
                ->color('success'),

            // Reklam Başarısı (FBCLID)
            Stat::make('Reklam Trafiği', Lead::whereNotNull('fbclid')->whereDate('created_at', $today)->count())
                ->description('Meta Ads üzerinden gelenler')
                ->descriptionIcon('heroicon-m-megaphone')
                ->chart($adsTrend ?: [0, 0])
                ->color('info'),

            // Cihaz Dağılımı (Mobil vs Desktop)
            Stat::make('Mobil Kullanım', "%{$mobilePercentage}")
                ->description($mobileToday . ' mobil kullanıcı')
                ->descriptionIcon($mobilePercentage > 50 ? 'heroicon-m-device-phone-mobile' : 'heroicon-m-computer-desktop')
                ->color($mobilePercentage > 50 ? 'primary' : 'gray')
                ->extraAttributes([
                    'class' => 'font-bold',
                ]),

            // Toplam Event (Deduplication Check)
            Stat::make('Toplam Event', Lead::whereDate('created_at', $today)->count())
                ->description('İşlenen toplam CAPI verisi')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('secondary'),
        ];
    }
}