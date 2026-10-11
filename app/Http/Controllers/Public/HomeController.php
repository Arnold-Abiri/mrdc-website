<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\DistrictStatistic;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\HomepageSlide;
use App\Models\InvestmentOpportunity;
use App\Models\Media;
use App\Models\Official;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function root(): RedirectResponse
    {
        $locale = session('public_locale', 'en');

        return redirect('/'.(in_array($locale, ['en', 'sn', 'nd'], true) ? $locale : 'en'));
    }

    public function index(): Response
    {
        return Inertia::render('Home', [
            'services' => Service::query()->with('translations')->public()->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'summary'])->map(fn (Service $service): array => $service->toLocalizedArray(['slug', 'name', 'summary'])),
            'documents' => Document::query()->with('translations')->public()->orderByDesc('published_at')->limit(5)->get(['id', 'slug', 'title', 'description'])->map(fn (Document $document): array => $document->toLocalizedArray(['slug', 'title', 'description'])),
            'news' => EditorialItem::query()->with(['translations', 'featuredMedia'])->public()->where('type', 'news')->orderByDesc('published_at')->limit(3)->get(['id', 'slug', 'title', 'summary', 'published_at', 'featured_media_id'])->map(fn (EditorialItem $item): array => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'published_at']), 'image_url' => $item->featured_media_id ? public_route('managed-media.show', $item->featured_media_id) : null, 'image_alt' => $item->featuredMedia?->alt_text, 'image_caption' => $item->featuredMedia?->caption]),
            'notices' => EditorialItem::query()->with('translations')->public()->where('type', 'notice')->orderByDesc('published_at')->limit(3)->get(['id', 'slug', 'title', 'summary', 'published_at'])->map(fn (EditorialItem $item): array => $item->toLocalizedArray(['slug', 'title', 'summary', 'published_at'])),
            'departments' => Department::query()->with('translations')->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary'])->map(fn (Department $department): array => $department->toLocalizedArray(['id', 'public_name', 'public_summary'])),
            'contacts' => PublicContact::query()->public()->orderBy('display_order')->limit(4)->get(['office', 'type', 'value']),
            'officials' => Official::query()->with('translations')->public()->orderBy('display_order')->orderBy('name')->limit(3)->get(['id', 'slug', 'name', 'title'])->map(fn (Official $official): array => $official->toLocalizedArray(['slug', 'name', 'title'])),
            'ward_count' => Ward::query()->public()->count(),
            'statistics' => DistrictStatistic::query()->with('translations')->public()->orderBy('display_order')->get(['id', 'label', 'value', 'unit', 'icon'])->map(fn (DistrictStatistic $statistic): array => $statistic->toLocalizedArray(['label', 'value', 'unit', 'icon'])),
            'tenders' => Tender::query()->with('translations')->public()->orderBy('display_order')->limit(3)->get(['id', 'slug', 'reference', 'title'])->map(fn (Tender $tender): array => [...$tender->toLocalizedArray(['slug', 'reference', 'title']), 'display_status' => $tender->displayStatus()]),
            'investment' => InvestmentOpportunity::query()->with('translations')->public()->orderBy('display_order')->limit(3)->get(['id', 'slug', 'title', 'sector', 'summary'])->map(fn (InvestmentOpportunity $opportunity): array => $opportunity->toLocalizedArray(['slug', 'title', 'sector', 'summary'])),
            'projects' => CouncilProject::query()->with('translations')->public()->orderBy('display_order')->limit(3)->get(['id', 'slug', 'title', 'project_status', 'summary'])->map(fn (CouncilProject $project): array => $project->toLocalizedArray(['slug', 'title', 'project_status', 'summary'])),
            'slides' => HomepageSlide::query()->with('translations')->public()->orderBy('display_order')->get(['id', 'headline', 'supporting_text', 'cta_label', 'cta_url', 'media_id'])->map(function (HomepageSlide $slide): array {
                $media = $slide->media_id ? Media::query()->where('id', $slide->media_id)->where('status', 'active')->first() : null;

                return [...$slide->toLocalizedArray(['headline', 'supporting_text', 'cta_label', 'cta_url']), 'image_url' => $media ? public_route('managed-media.show', $media->id) : null];
            }),
            'meetings' => CouncilMeeting::query()->with('translations')->public()->where('meeting_status', 'scheduled')->orderBy('scheduled_date')->orderBy('display_order')->limit(3)->get(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'agenda_document_id', 'minutes_document_id'])->map(fn (CouncilMeeting $meeting): array => [
                ...$meeting->toLocalizedArray(['title', 'venue']),
                'id' => $meeting->id,
                'meeting_type' => $meeting->meeting_type,
                'scheduled_date' => $meeting->scheduled_date?->format('Y-m-d'),
                'scheduled_time' => $meeting->scheduled_time,
                'has_agenda' => ! empty($meeting->agenda_document_id),
                'has_minutes' => ! empty($meeting->minutes_document_id),
            ]),
        ]);
    }
}
