<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class MetaConversionStats extends BaseWidget
{
    // Widget'ın her 15 saniyede bir kendini yenilemesini sağlar (Canlı takip)
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        return [
            
            Stat::make('WhatsApp İletişim', Lead::where('type', 'whatsapp')->whereDate('created_at', $today)->count())
                ->description('Bugün gelen talepler')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->chart(Lead::where('type', 'whatsapp')->where('created_at', '>=', now()->subDays(7))->pluck('id')->toArray()) // Küçük bir grafik çizgisi
                ->color('success'),

            // 2. Menü Görüntüleme (PWA)
            Stat::make('Menü Tıklaması', Lead::where('type', 'menu')->whereDate('created_at', $today)->count())
                ->description('Bugün menüyü açanlar')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            // 3. Reklam Verimi (FBC Parametresi olanlar)
            Stat::make('Reklam Tıklamaları', Lead::whereNotNull('fbclid')->whereDate('created_at', $today)->count())
                ->description('Facebook/Instagram reklam başarısı')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'title' => 'Bu veri doğrudan reklamlardan gelen müşterileri gösterir',
                ]),
        ];
    }
}