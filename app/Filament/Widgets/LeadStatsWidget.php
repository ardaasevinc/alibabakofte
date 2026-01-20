<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Lead;
use Carbon\Carbon;

class LeadStatsWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '30s'; // otomatik yenileme

    protected function getStats(): array
    {
        $total = Lead::count();
        $today = Lead::whereDate('created_at', Carbon::today())->count();
        $unique = Lead::distinct('browser_id')->count('browser_id');

        $rate = $total > 0 ? round(($today / $total) * 100, 1) : 0;

        return [
            Stat::make('Toplam Lead', $total)
                ->description('Tüm zaman toplam lead')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Bugünkü Lead', $today)
                ->description('Son 24 saat')
                ->icon('heroicon-o-bolt')
                ->color('success'),

            Stat::make('Eşsiz Ziyaret (Browser)', $unique)
                ->description('Tekil browser ID')
                ->icon('heroicon-o-eye')
                ->color('warning'),

            Stat::make('Dönüşüm Oranı', "%{$rate}")
                ->description('Bugün / Toplam oran')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}
