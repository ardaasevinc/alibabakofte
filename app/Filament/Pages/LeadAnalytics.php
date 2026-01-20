<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'CAPI Analitiği';
    protected static ?string $title = 'Meta CAPI Analitiği';
    protected static ?string $slug = 'lead-analytics';

    protected static string $view = 'filament.pages.lead-analytics';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function getStats(): array
    {
        $today = Carbon::today();

        $totalLeads = Lead::count();

        $todayLeads = Lead::whereDate('created_at', $today)->count();

        $uniquePageViews = Lead::distinct('session_hash')->count('session_hash');

        $conversionRate = $uniquePageViews > 0
            ? round(($totalLeads / $uniquePageViews) * 100, 1)
            : 0;

        return [
            'total' => $totalLeads,
            'today' => $todayLeads,
            'unique' => $uniquePageViews,
            'rate'  => $conversionRate,
        ];
    }

    public function getUtmStats()
    {
        return Lead::select('utm_source', DB::raw('count(*) as total'))
            ->whereNotNull('utm_source')
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function getBrowserStats()
    {
        return Lead::select('browser_id', DB::raw('count(*) as total'))
            ->whereNotNull('browser_id')
            ->groupBy('browser_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function getDeviceStats()
    {
        return Lead::select('device_id', DB::raw('count(*) as total'))
            ->groupBy('device_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function getDailyLeadTrend()
    {
        return Lead::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
