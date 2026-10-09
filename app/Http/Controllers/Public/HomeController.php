<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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
            'services' => Service::query()->with('translations')->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary']),
            'documents' => Document::query()->with('translations')->public()->orderByDesc('published_at')->limit(5)->get(['slug', 'title', 'description']),
            'news' => EditorialItem::query()->with('translations')->public()->where('type', 'news')->orderByDesc('published_at')->limit(3)->get(['slug', 'title', 'summary', 'published_at']),
            'notices' => EditorialItem::query()->with('translations')->public()->where('type', 'notice')->orderByDesc('published_at')->limit(3)->get(['slug', 'title', 'summary', 'published_at']),
            'departments' => Department::query()->with('translations')->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary']),
            'contacts' => PublicContact::query()->public()->orderBy('display_order')->limit(4)->get(['office', 'type', 'value']),
            'officials' => Official::query()->public()->orderBy('display_order')->orderBy('name')->limit(3)->get(['slug', 'name', 'title']),
            'ward_count' => Ward::query()->public()->count(),
            'statistics' => DistrictStatistic::query()->public()->orderBy('display_order')->get(['label', 'value', 'unit', 'icon']),
            'tenders' => Tender::query()->with('translations')->public()->orderBy('display_order')->limit(3)->get(['slug', 'reference', 'title'])->map(fn (Tender $tender): array => [...$tender->toLocalizedArray(['slug', 'reference', 'title']), 'display_status' => $tender->displayStatus()]),
            'investment' => InvestmentOpportunity::query()->with('translations')->public()->orderBy('display_order')->limit(3)->get(['slug', 'title', 'sector', 'summary']),
            'projects' => CouncilProject::query()->with('translations')->public()->orderBy('display_order')->limit(3)->get(['slug', 'title', 'project_status', 'summary']),
            'slides' => HomepageSlide::query()->public()->orderBy('display_order')->get(['headline', 'supporting_text', 'cta_label', 'cta_url', 'media_id'])->map(function (HomepageSlide $slide): array {
                $media = $slide->media_id ? Media::query()->where('id', $slide->media_id)->where('status', 'active')->first() : null;

                return [...$slide->only(['headline', 'supporting_text', 'cta_label', 'cta_url']), 'image_url' => $media ? public_route('managed-media.show', $media->id) : null];
            }),
        ]);
    }
}
