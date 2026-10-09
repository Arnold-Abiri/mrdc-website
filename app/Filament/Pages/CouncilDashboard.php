<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\Meetings\CouncilMeetingResource;
use App\Filament\Resources\Projects\CouncilProjectResource;
use App\Filament\Resources\Tenders\TenderResource;
use App\Filament\Resources\Vacancies\VacancyResource;
use Filament\Pages\Dashboard;
use Illuminate\Database\Eloquent\Collection;

class CouncilDashboard extends Dashboard
{
    protected string $view = 'filament.pages.council-dashboard';

    public function getHeading(): ?string
    {
        return null;
    }

    /** @return array<string> */
    public function getPageClasses(): array
    {
        return ['mrdc-council-page'];
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $news = $this->records(EditorialItemResource::class, fn ($query) => $query->with('featuredMedia')->where('type', 'news')->latest('created_at')->limit(3)->get());
        $meetings = $this->records(CouncilMeetingResource::class, fn ($query) => $query->whereDate('scheduled_date', '>=', today())->where('meeting_status', 'scheduled')->orderBy('scheduled_date')->limit(3)->get());
        $documents = $this->records(DocumentResource::class, fn ($query) => $query->latest('created_at')->limit(4)->get());
        $projects = $this->records(CouncilProjectResource::class, fn ($query) => $query->with('featuredMedia')->latest('created_at')->limit(4)->get());
        $enquiries = $this->records(EnquiryResource::class, fn ($query) => $query->latest('submitted_at')->limit(4)->get());

        // Pending approvals from editorial items
        $editorialApprovals = $this->records(EditorialItemResource::class, fn ($query) => $query->where(fn ($q) => $q->where('status', 'draft')->orWhere('verification_status', 'pending'))->latest('updated_at')->limit(4)->get())
            ->map(fn ($item) => [
                'title' => $item->title,
                'type' => ucfirst($item->type ?? 'News'),
                'tone' => match ($item->type) {
                    'notice' => 'orange',
                    'speeches' => 'purple',
                    default => 'blue',
                },
                'date' => $item->updated_at?->format('d M Y') ?? now()->format('d M Y'),
                'status' => $item->verification_status === 'pending' ? 'Under review' : 'Pending',
                'url' => EditorialItemResource::getUrl('edit', ['record' => $item]),
            ]);

        // Calculate metrics with month-over-month change
        $startOfThisMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        $calcMetric = function (string $resource, callable $baseFilter, string $dateColumn = 'created_at') use ($startOfThisMonth, $startOfLastMonth, $endOfLastMonth) {
            $total = $this->count($resource, fn ($q) => $baseFilter($q));
            $thisMonthCount = $this->count($resource, fn ($q) => $baseFilter($q)->where($dateColumn, '>=', $startOfThisMonth));
            $lastMonthCount = $this->count($resource, fn ($q) => $baseFilter($q)->whereBetween($dateColumn, [$startOfLastMonth, $endOfLastMonth]));
            $diff = $thisMonthCount - $lastMonthCount;

            return [
                'total' => $total,
                'diff' => $diff,
                'diff_label' => ($diff >= 0 ? "+{$diff}" : "{$diff}").' from last month',
                'is_up' => $diff >= 0,
            ];
        };

        $newsStats = $calcMetric(EditorialItemResource::class, fn ($q) => $q->where('type', 'news')->where('status', 'published'), 'published_at');
        $meetingStats = $calcMetric(CouncilMeetingResource::class, fn ($q) => $q->whereDate('scheduled_date', '>=', today())->where('meeting_status', 'scheduled'), 'scheduled_date');
        $tenderStats = $calcMetric(TenderResource::class, fn ($q) => $q->where('status', 'published')->whereNotIn('lifecycle_status', ['closed', 'cancelled', 'awarded']), 'created_at');
        $vacancyStats = $calcMetric(VacancyResource::class, fn ($q) => $q->where('status', 'published')->where(fn ($sub) => $sub->whereNull('closes_at')->orWhereDate('closes_at', '>=', today())), 'created_at');
        $projectStats = $calcMetric(CouncilProjectResource::class, fn ($q) => $q->where('status', 'published'), 'created_at');

        $metrics = [
            [
                'label' => 'News Articles',
                'value' => $newsStats['total'],
                'change' => $newsStats['diff_label'],
                'change_tone' => $newsStats['is_up'] ? 'up' : 'down',
                'tone' => 'blue',
                'icon' => 'news',
            ],
            [
                'label' => 'Upcoming Meetings',
                'value' => $meetingStats['total'],
                'change' => $meetingStats['diff_label'],
                'change_tone' => $meetingStats['is_up'] ? 'up' : 'down',
                'tone' => 'green',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Active Tenders',
                'value' => $tenderStats['total'],
                'change' => $tenderStats['diff_label'],
                'change_tone' => $tenderStats['is_up'] ? 'up' : 'down',
                'tone' => 'orange',
                'icon' => 'tender',
            ],
            [
                'label' => 'Open Vacancies',
                'value' => $vacancyStats['total'],
                'change' => $vacancyStats['diff_label'],
                'change_tone' => $vacancyStats['is_up'] ? 'up' : 'down',
                'tone' => 'red',
                'icon' => 'briefcase',
            ],
            [
                'label' => 'Development Projects',
                'value' => $projectStats['total'],
                'change' => $projectStats['diff_label'],
                'change_tone' => $projectStats['is_up'] ? 'up' : 'down',
                'tone' => 'purple',
                'icon' => 'project',
            ],
        ];

        $contentTypes = [
            ['label' => 'News Articles', 'value' => $newsStats['total'], 'tone' => 'blue'],
            ['label' => 'Meetings & Events', 'value' => $meetingStats['total'], 'tone' => 'green'],
            ['label' => 'Public Tenders', 'value' => $tenderStats['total'], 'tone' => 'orange'],
            ['label' => 'Vacancies', 'value' => $vacancyStats['total'], 'tone' => 'red'],
            ['label' => 'Council Projects', 'value' => $projectStats['total'], 'tone' => 'purple'],
            ['label' => 'Official Documents', 'value' => $this->count(DocumentResource::class, fn ($query) => $query->where('status', 'published')), 'tone' => 'teal'],
        ];

        $monthlyNews = $this->records(EditorialItemResource::class, fn ($query) => $query->where('type', 'news')->where('status', 'published')->where('published_at', '>=', now()->startOfMonth()->subMonths(11))->get(['published_at']));
        $monthlyDocuments = $this->records(DocumentResource::class, fn ($query) => $query->where('status', 'published')->where('published_at', '>=', now()->startOfMonth()->subMonths(11))->get(['published_at']));
        $monthlyProjects = $this->records(CouncilProjectResource::class, fn ($query) => $query->where('status', 'published')->where('published_at', '>=', now()->startOfMonth()->subMonths(11))->get(['published_at']));
        $monthlyMeetings = $this->records(CouncilMeetingResource::class, fn ($query) => $query->where('status', 'published')->where('published_at', '>=', now()->startOfMonth()->subMonths(11))->get(['published_at']));
        $months = collect(range(11, 0))->map(function (int $offset) use ($monthlyNews, $monthlyDocuments, $monthlyProjects, $monthlyMeetings): array {
            $month = now()->startOfMonth()->subMonths($offset);
            $key = $month->format('Y-m');

            return [
                'label' => $month->format('M'),
                'news' => $monthlyNews->filter(fn ($item): bool => $item->published_at?->format('Y-m') === $key)->count(),
                'documents' => $monthlyDocuments->filter(fn ($item): bool => $item->published_at?->format('Y-m') === $key)->count(),
                'projects' => $monthlyProjects->filter(fn ($item): bool => $item->published_at?->format('Y-m') === $key)->count(),
                'meetings' => $monthlyMeetings->filter(fn ($item): bool => $item->published_at?->format('Y-m') === $key)->count(),
            ];
        });

        $approvals = $editorialApprovals;

        return compact('news', 'meetings', 'documents', 'projects', 'enquiries', 'approvals', 'metrics', 'contentTypes', 'months');
    }

    private function records(string $resource, callable $callback): Collection
    {
        return $resource::canViewAny() ? $callback($resource::getEloquentQuery()) : new Collection;
    }

    private function count(string $resource, callable $callback): int
    {
        return $resource::canViewAny() ? $callback($resource::getEloquentQuery())->count() : 0;
    }
}
