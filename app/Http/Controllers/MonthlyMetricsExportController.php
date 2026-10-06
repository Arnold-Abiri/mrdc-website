<?php

namespace App\Http\Controllers;

use App\Domain\Analytics\AnalyticsReporter;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonthlyMetricsExportController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        Gate::authorize('analytics.export');
        $month = is_string(request()->query('month')) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', request()->query('month')) ? request()->query('month') : now()->format('Y-m');
        $start = $month.'-01';
        $end = date('Y-m-t', strtotime($start));
        $reporter = app(AnalyticsReporter::class);
        $kpis = $reporter->kpis($start, $end);
        $filename = "monthly-metrics-{$month}.csv";

        return response()->streamDownload(function () use ($kpis, $reporter, $start, $end): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['metric', 'value']);
            foreach ($kpis as $metric => $value) {
                fputcsv($handle, [$metric, (string) $value]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['referrer_category', 'events']);
            foreach ($reporter->referrals($start, $end) as $row) {
                fputcsv($handle, [$row['category'], (string) $row['events']]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
