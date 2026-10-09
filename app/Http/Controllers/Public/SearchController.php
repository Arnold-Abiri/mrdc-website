<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\InvestmentOpportunity;
use App\Models\Official;
use App\Models\Page;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use App\Models\Ward;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $rawQuery = $request->query('q', '');
        abort_unless(is_string($rawQuery), 422);
        $query = trim($rawQuery);
        abort_if(mb_strlen($query) > 100, 422);

        $pages = $query === '' ? collect() : Page::query()->with('translations')->public()
            ->where(function ($builder) use ($query): void {
                $escaped = addcslashes($query, '%_\\');
                $builder->where('title', 'like', '%'.$escaped.'%')
                    ->orWhere('summary', 'like', '%'.$escaped.'%');
            })
            ->orderBy('title')->limit(20)->get(['slug', 'title', 'summary', 'verification_status']);

        $documents = $query === '' ? collect() : Document::query()->with('translations')->public()
            ->where(function ($builder) use ($query): void {
                $escaped = addcslashes($query, '%_\\');
                $builder->where('title', 'like', '%'.$escaped.'%')
                    ->orWhere('description', 'like', '%'.$escaped.'%');
            })
            ->orderBy('title')->limit(20)->get(['slug', 'title', 'description']);

        $pageResults = $pages->map(fn (Page $page): array => [
            'type' => 'Page', 'title' => $page->title, 'summary' => $page->summary,
            'url' => public_route('pages.show', $page->slug),
            'is_review_content' => $page->verification_status !== 'publishable',
        ]);
        $documentResults = $documents->map(fn (Document $document): array => [
            'type' => 'Document', 'title' => $document->title, 'summary' => $document->description,
            'url' => public_route('documents.show', $document->slug), 'is_review_content' => false,
        ]);
        $services = $query === '' ? collect() : Service::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('name', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('name')->limit(20)->get(['slug', 'name', 'summary']);
        $serviceResults = $services->map(fn (Service $service): array => [
            'type' => 'Service', 'title' => $service->name, 'summary' => $service->summary,
            'url' => public_route('services.show', $service->slug), 'is_review_content' => false,
        ]);
        $departments = $query === '' ? collect() : Department::query()->with('translations')->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('public_name', 'like', '%'.$escaped.'%')->orWhere('public_summary', 'like', '%'.$escaped.'%')->orWhere('public_description', 'like', '%'.$escaped.'%');
        })->orderBy('public_display_order')->orderBy('public_name')->limit(20)->get(['id', 'public_name', 'public_summary']);
        $departmentResults = $departments->map(fn (Department $department): array => [
            'type' => 'Department', 'title' => $department->public_name, 'summary' => $department->public_summary,
            'url' => public_route('departments.public.show', $department->id), 'is_review_content' => false,
        ]);
        $editorial = $query === '' ? collect() : EditorialItem::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('body', 'like', '%'.$escaped.'%');
        })->orderByDesc('published_at')->limit(20)->get(['type', 'slug', 'title', 'summary']);
        $editorialResults = $editorial->map(fn (EditorialItem $item): array => [
            'type' => $item->type === 'news' ? 'News' : 'Notice', 'title' => $item->title, 'summary' => $item->summary,
            'url' => route($item->type === 'news' ? 'news.show' : 'notices.show', $item->slug), 'is_review_content' => false,
        ]);
        $wards = $query === '' ? collect() : Ward::query()->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('name', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%')->orWhere('boundaries_description', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('name')->limit(20)->get(['slug', 'name', 'description']);
        $wardResults = $wards->map(fn (Ward $ward): array => [
            'type' => 'Ward', 'title' => $ward->name, 'summary' => $ward->description,
            'url' => public_route('wards.show', $ward->slug), 'is_review_content' => false,
        ]);
        $officials = $query === '' ? collect() : Official::query()->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('name', 'like', '%'.$escaped.'%')->orWhere('title', 'like', '%'.$escaped.'%')->orWhere('biography', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('name')->limit(20)->get(['slug', 'name', 'title', 'biography']);
        $officialResults = $officials->map(fn (Official $official): array => [
            'type' => 'Official', 'title' => $official->name, 'summary' => $official->title,
            'url' => public_route('officials.show', $official->slug), 'is_review_content' => false,
        ]);

        $tenders = $query === '' ? collect() : Tender::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('reference', 'like', '%'.$escaped.'%')->orWhere('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'description']);
        $tenderResults = $tenders->map(fn (Tender $tender): array => [
            'type' => 'Tender', 'title' => $tender->title, 'summary' => null,
            'url' => public_route('tenders.show', $tender->slug), 'is_review_content' => false,
        ]);
        $vacancies = $query === '' ? collect() : Vacancy::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'description']);
        $vacancyResults = $vacancies->map(fn (Vacancy $vacancy): array => [
            'type' => 'Vacancy', 'title' => $vacancy->title, 'summary' => null,
            'url' => public_route('vacancies.show', $vacancy->slug), 'is_review_content' => false,
        ]);
        $investment = $query === '' ? collect() : InvestmentOpportunity::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'summary']);
        $investmentResults = $investment->map(fn (InvestmentOpportunity $opportunity): array => [
            'type' => 'Investment', 'title' => $opportunity->title, 'summary' => $opportunity->summary,
            'url' => public_route('investment.show', $opportunity->slug), 'is_review_content' => false,
        ]);
        $meetings = $query === '' ? collect() : CouncilMeeting::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('venue', 'like', '%'.$escaped.'%');
        })->orderBy('scheduled_date')->limit(20)->get(['id', 'title', 'summary']);
        $meetingResults = $meetings->map(fn (CouncilMeeting $meeting): array => [
            'type' => 'Meeting', 'title' => $meeting->title, 'summary' => $meeting->summary,
            'url' => public_route('meetings.show', $meeting->id), 'is_review_content' => false,
        ]);
        $projects = $query === '' ? collect() : CouncilProject::query()->with('translations')->public()->where(function ($builder) use ($query): void {
            $escaped = addcslashes($query, '%_\\');
            $builder->where('title', 'like', '%'.$escaped.'%')->orWhere('summary', 'like', '%'.$escaped.'%')->orWhere('description', 'like', '%'.$escaped.'%');
        })->orderBy('display_order')->orderBy('title')->limit(20)->get(['slug', 'title', 'summary']);
        $projectResults = $projects->map(fn (CouncilProject $project): array => [
            'type' => 'Project', 'title' => $project->title, 'summary' => $project->summary,
            'url' => public_route('projects.show', $project->slug), 'is_review_content' => false,
        ]);

        return Inertia::render('Search', ['query' => $query, 'results' => $pageResults->concat($documentResults)->concat($serviceResults)->concat($departmentResults)->concat($editorialResults)->concat($wardResults)->concat($officialResults)->concat($tenderResults)->concat($vacancyResults)->concat($investmentResults)->concat($meetingResults)->concat($projectResults)->take(20)->values()]);
    }
}
