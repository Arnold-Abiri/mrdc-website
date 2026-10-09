<?php

namespace App\Filament\Pages;

use App\Domain\Analytics\AnalyticsReporter;
use BackedEnum;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;

class AnalyticsDashboard extends FilamentPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'M&E dashboard';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Monitoring';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Monitoring & Evaluation';

    protected string $view = 'filament.pages.analytics-dashboard';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('analytics.view');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $period = is_string(request()->query('period')) ? request()->query('period') : 'last_30';
        $range = AnalyticsReporter::resolvePeriod($period, is_string(request()->query('from')) ? request()->query('from') : null, is_string(request()->query('to')) ? request()->query('to') : null);
        $reporter = app(AnalyticsReporter::class);
        $newsViews = [...$reporter->popularContent($range['from'], $range['to'], 'news', 5), ...$reporter->popularContent($range['from'], $range['to'], 'notice', 5)];
        usort($newsViews, fn ($a, $b): int => $b['views'] <=> $a['views']);

        return [
            'period' => $period,
            'from' => $range['from'],
            'to' => $range['to'],
            'kpis' => $reporter->kpis($range['from'], $range['to']),
            'trend' => $reporter->dailyTrend($range['from'], $range['to']),
            'popular' => $reporter->popularContent($range['from'], $range['to']),
            'tenders' => $reporter->popularContent($range['from'], $range['to'], 'tender', 5),
            'vacancies' => $reporter->popularContent($range['from'], $range['to'], 'vacancy', 5),
            'news' => array_slice($newsViews, 0, 5),
            'documents' => $reporter->popularDocuments($range['from'], $range['to']),
            'referrals' => $reporter->referrals($range['from'], $range['to']),
            'locales' => $reporter->locales($range['from'], $range['to']),
            'activity' => $reporter->contentActivity($range['from'], $range['to'], 20),
        ];
    }
}
