<?php

namespace App\Filament\Pages;

use App\Domain\Analytics\AnalyticsReporter;
use App\Models\ErrorEvent;
use App\Models\Incident;
use BackedEnum;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;

class MonthlyReport extends FilamentPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Monthly report';

    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Monitoring';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Monthly performance report';

    protected string $view = 'filament.pages.monthly-report';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('analytics.view');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $month = is_string(request()->query('month')) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', request()->query('month')) ? request()->query('month') : now()->format('Y-m');
        $start = $month.'-01';
        $end = date('Y-m-t', strtotime($start));
        $reporter = app(AnalyticsReporter::class);
        $incidents = Incident::query()->where('started_at', '<=', $end.' 23:59:59')->where(fn ($query) => $query->whereNull('recovered_at')->orWhere('recovered_at', '>=', $start.' 00:00:00'))->orderBy('started_at')->get();
        $errors = ErrorEvent::query()->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)
            ->selectRaw('exception_class, COUNT(*) as events')->groupBy('exception_class')->orderByDesc('events')->get();

        return [
            'month' => $month,
            'from' => $start,
            'to' => $end,
            'kpis' => $reporter->kpis($start, $end),
            'referrals' => $reporter->referrals($start, $end),
            'locales' => $reporter->locales($start, $end),
            'documents' => $reporter->popularDocuments($start, $end, 5),
            'activity' => $reporter->contentActivity($start, $end, 30),
            'incidents' => $incidents,
            'errors' => $errors,
        ];
    }
}
