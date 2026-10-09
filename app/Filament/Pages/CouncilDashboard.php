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
        $approvals = $this->records(EditorialItemResource::class, fn ($query) => $query->where('status', 'draft')->where('verification_status', 'verified')->latest('updated_at')->limit(4)->get());

        $metrics = [
            ['label' => 'News articles', 'value' => $this->count(EditorialItemResource::class, fn ($query) => $query->where('type', 'news')->where('status', 'published')), 'tone' => 'blue', 'icon' => 'news'],
            ['label' => 'Upcoming meetings', 'value' => $this->count(CouncilMeetingResource::class, fn ($query) => $query->whereDate('scheduled_date', '>=', today())->where('meeting_status', 'scheduled')), 'tone' => 'green', 'icon' => 'calendar'],
            ['label' => 'Open tenders', 'value' => $this->count(TenderResource::class, fn ($query) => $query->where('status', 'published')->whereNotIn('lifecycle_status', ['closed', 'cancelled', 'awarded'])->where(fn ($q) => $q->whereNull('closes_at')->orWhere('closes_at', '>=', now()))), 'tone' => 'orange', 'icon' => 'file'],
            ['label' => 'Open vacancies', 'value' => $this->count(VacancyResource::class, fn ($query) => $query->where('status', 'published')->where(fn ($q) => $q->whereNull('closes_at')->orWhereDate('closes_at', '>=', today()))), 'tone' => 'red', 'icon' => 'briefcase'],
            ['label' => 'Development projects', 'value' => $this->count(CouncilProjectResource::class, fn ($query) => $query->where('status', 'published')), 'tone' => 'purple', 'icon' => 'chart'],
        ];

        $contentTypes = [
            ['label' => 'News', 'value' => $metrics[0]['value'], 'tone' => 'blue'],
            ['label' => 'Meetings', 'value' => $metrics[1]['value'], 'tone' => 'green'],
            ['label' => 'Tenders', 'value' => $metrics[2]['value'], 'tone' => 'orange'],
            ['label' => 'Vacancies', 'value' => $metrics[3]['value'], 'tone' => 'red'],
            ['label' => 'Projects', 'value' => $metrics[4]['value'], 'tone' => 'purple'],
            ['label' => 'Documents', 'value' => $this->count(DocumentResource::class, fn ($query) => $query->where('status', 'published')), 'tone' => 'teal'],
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
