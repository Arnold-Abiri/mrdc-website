<?php

namespace App\Domain\Analytics;

use App\Models\AuditEvent;
use App\Models\CouncilProject;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\InvestmentOpportunity;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsReporter
{
    /**
     * @return array{from: string, to: string}
     */
    public static function resolvePeriod(string $period, ?string $from = null, ?string $to = null): array
    {
        $today = today();

        return match ($period) {
            'today' => ['from' => $today->toDateString(), 'to' => $today->toDateString()],
            'last_7' => ['from' => $today->copy()->subDays(6)->toDateString(), 'to' => $today->toDateString()],
            'month' => ['from' => $today->copy()->startOfMonth()->toDateString(), 'to' => $today->toDateString()],
            'previous_month' => ['from' => $today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(), 'to' => $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString()],
            'year' => ['from' => $today->copy()->startOfYear()->toDateString(), 'to' => $today->toDateString()],
            'custom' => ['from' => $from ?? $today->copy()->subDays(29)->toDateString(), 'to' => $to ?? $today->toDateString()],
            default => ['from' => $today->copy()->subDays(29)->toDateString(), 'to' => $today->toDateString()],
        };
    }

    /**
     * @return array<string, int>
     */
    public function kpis(string $from, string $to): array
    {
        $events = DB::table('analytics_events')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
        $content = (clone $events)->where('event_type', 'content_view');
        $kpis = [
            'visitor_days' => (clone $events)->distinct('visitor_key')->count('visitor_key'),
            'sessions' => (clone $events)->distinct('visitor_key')->count('visitor_key'),
            'page_views' => (clone $events)->where('event_type', 'page_view')->count(),
            'content_views' => (clone $content)->count(),
            'tender_views' => (clone $content)->where('subject_type', 'tender')->count(),
            'vacancy_views' => (clone $content)->where('subject_type', 'vacancy')->count(),
            'news_views' => (clone $content)->whereIn('subject_type', ['news', 'notice'])->count(),
            'downloads' => DB::table('document_downloads')->whereDate('downloaded_at', '>=', $from)->whereDate('downloaded_at', '<=', $to)->count(),
            'content_updates' => AuditEvent::query()->where('created_at', '>=', $from.' 00:00:00')->where('created_at', '<=', $to.' 23:59:59')->where(fn ($query) => $query->where('action', 'like', '%.published')->orWhere('action', 'like', '%.updated')->orWhere('action', 'like', 'documents.replaced'))->count(),
        ];

        return $kpis;
    }

    /**
     * @return list<array{date: string, page_views: int, content_views: int, visitor_days: int}>
     */
    public function dailyTrend(string $from, string $to): array
    {
        $rows = DB::table('analytics_events')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('DATE(created_at) as date, SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) as page_views, SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) as content_views, COUNT(DISTINCT visitor_key) as visitor_days', ['page_view', 'content_view'])
            ->groupByRaw('DATE(created_at)')->orderBy('date')->get();
        $byDate = [];
        foreach ($rows as $row) {
            $byDate[$row->date] = ['date' => $row->date, 'page_views' => (int) $row->page_views, 'content_views' => (int) $row->content_views, 'visitor_days' => (int) $row->visitor_days];
        }
        $trend = [];
        $cursor = Carbon::parse($from);
        while ($cursor->toDateString() <= $to) {
            $day = $cursor->toDateString();
            $trend[] = $byDate[$day] ?? ['date' => $day, 'page_views' => 0, 'content_views' => 0, 'visitor_days' => 0];
            $cursor->addDay();
        }

        return $trend;
    }

    /**
     * @return list<array{title: string, url: string, views: int}>
     */
    public function popularContent(string $from, string $to, ?string $subjectType = null, int $limit = 10): array
    {
        $rows = DB::table('analytics_events')->where('event_type', 'content_view')
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->when($subjectType, fn ($query) => $query->where('subject_type', $subjectType))
            ->selectRaw('subject_type, subject, COUNT(*) as views')->groupBy(['subject_type', 'subject'])
            ->orderByDesc('views')->limit($limit)->get();

        return $rows->map(fn ($row): array => [
            'title' => $this->resolveTitle((string) $row->subject_type, (string) $row->subject),
            'url' => $row->subject ?? '',
            'views' => (int) $row->views,
        ])->all();
    }

    /**
     * @return list<array{title: string, downloads: int}>
     */
    public function popularDocuments(string $from, string $to, int $limit = 10): array
    {
        $rows = DB::table('document_downloads')->join('documents', 'documents.id', '=', 'document_downloads.document_id')
            ->whereDate('downloaded_at', '>=', $from)->whereDate('downloaded_at', '<=', $to)
            ->selectRaw('documents.title, documents.category, COUNT(*) as downloads')
            ->groupBy(['documents.title', 'documents.category'])->orderByDesc('downloads')->limit($limit)->get();

        return $rows->map(fn ($row): array => ['title' => (string) $row->title, 'downloads' => (int) $row->downloads])->all();
    }

    /**
     * @return list<array{category: string, events: int}>
     */
    public function referrals(string $from, string $to): array
    {
        return DB::table('analytics_events')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('referrer_category as category, COUNT(*) as events')->groupBy('referrer_category')
            ->orderByDesc('events')->get()->map(fn ($row): array => ['category' => (string) $row->category, 'events' => (int) $row->events])->all();
    }

    /**
     * @return list<array{locale: string, views: int}>
     */
    public function locales(string $from, string $to): array
    {
        return DB::table('analytics_events')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->selectRaw('locale, COUNT(*) as views')->groupBy('locale')->orderByDesc('views')->get()
            ->map(fn ($row): array => ['locale' => (string) ($row->locale ?? 'unknown'), 'views' => (int) $row->views])->all();
    }

    /**
     * @return list<array{date: string, action: string, subject: string}>
     */
    public function contentActivity(string $from, string $to, int $limit = 50): array
    {
        return AuditEvent::query()->where('created_at', '>=', $from.' 00:00:00')->where('created_at', '<=', $to.' 23:59:59')
            ->where(fn ($query) => $query->where('action', 'like', '%.published')->orWhere('action', 'like', '%.updated')->orWhere('action', 'like', 'documents.replaced'))
            ->orderByDesc('created_at')->limit($limit)->get()
            ->map(fn (AuditEvent $event): array => ['date' => (string) $event->created_at, 'action' => $event->action, 'subject' => $event->subject_type.' #'.$event->subject_id])->all();
    }

    private function resolveTitle(string $type, string $subject): string
    {
        $model = match ($type) {
            'tender' => Tender::query()->where('slug', $subject)->first(['title']),
            'vacancy' => Vacancy::query()->where('slug', $subject)->first(['title']),
            'news', 'notice' => EditorialItem::query()->where('slug', $subject)->first(['title']),
            'service' => Service::query()->where('slug', $subject)->first(['name as title']),
            'document' => Document::query()->where('slug', $subject)->first(['title']),
            'project' => CouncilProject::query()->where('slug', $subject)->first(['title']),
            'investment' => InvestmentOpportunity::query()->where('slug', $subject)->first(['title']),
            default => null,
        };

        if ($model === null) {
            return $subject;
        }

        return (string) $model->title;
    }
}
