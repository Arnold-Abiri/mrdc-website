<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\DistrictStatistic;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\HomepageSlide;
use App\Models\InvestmentOpportunity;
use App\Models\Media;
use App\Models\Official;
use App\Models\Page;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Ward;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Stakeholder preview (signed URLs only).
 *
 * Displays draft + demo-classified records so council officials can review
 * a complete-looking site before approving anything for publication.
 * Never linked publicly, never in the sitemap, always noindexed via the
 * X-Robots-Tag header applied in the route definition.
 */
class PreviewController extends Controller
{
    public function home(): Response
    {
        session(['preview_authorized' => true]);

        return Inertia::render('Home', [
            'preview' => true,
            'services' => Service::query()->with('translations')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'summary'])->map(fn (Service $service): array => $service->toLocalizedArray(['slug', 'name', 'summary'])),
            'documents' => Document::query()->with('translations')->whereIn('status', ['draft', 'published'])->orderBy('title')->limit(5)->get(['id', 'slug', 'title', 'description'])->map(fn (Document $document): array => $document->toLocalizedArray(['slug', 'title', 'description'])),
            'news' => EditorialItem::query()->with(['translations', 'featuredMedia.translations'])->where('type', 'news')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->limit(3)->get(['id', 'slug', 'title', 'summary', 'published_at', 'featured_media_id'])->map(fn (EditorialItem $item): array => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'published_at']), 'image_url' => $item->featured_media_id ? public_route('managed-media.show', $item->featured_media_id) : null, 'image_alt' => $item->featuredMedia?->translated('alt_text'), 'image_caption' => $item->featuredMedia?->translated('caption')]),
            'notices' => EditorialItem::query()->with('translations')->where('type', 'notice')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->limit(3)->get(['id', 'slug', 'title', 'summary', 'published_at'])->map(fn (EditorialItem $item): array => $item->toLocalizedArray(['slug', 'title', 'summary', 'published_at'])),
            'departments' => Department::query()->with('translations')->where('status', 'active')->orderBy('public_display_order')->limit(4)->get(['id', 'public_name', 'public_summary'])->map(fn (Department $department): array => $department->toLocalizedArray(['id', 'public_name', 'public_summary'])),
            'contacts' => PublicContact::query()->with('translations')->orderBy('display_order')->limit(4)->get(['office', 'type', 'value'])->map(fn ($contact): array => $contact->toLocalizedArray(['office', 'type', 'value'])),
            'officials' => Official::query()->with('translations')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->limit(3)->get(['id', 'slug', 'name', 'title'])->map(fn (Official $official): array => $official->toLocalizedArray(['slug', 'name', 'title'])),
            'ward_count' => Ward::query()->whereIn('status', ['draft', 'published'])->count(),
            'statistics' => DistrictStatistic::query()->with('translations')->public()->orderBy('display_order')->get(['id', 'label', 'value', 'unit', 'icon'])->map(fn (DistrictStatistic $statistic): array => $statistic->toLocalizedArray(['label', 'value', 'unit', 'icon'])),
            'tenders' => Tender::query()->with('translations')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->limit(3)->get(['id', 'slug', 'reference', 'title'])->map(fn (Tender $tender): array => [...$tender->toLocalizedArray(['slug', 'reference', 'title']), 'display_status' => $tender->displayStatus()]),
            'investment' => InvestmentOpportunity::query()->with('translations')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->limit(3)->get(['id', 'slug', 'title', 'sector', 'summary'])->map(fn (InvestmentOpportunity $opportunity): array => $opportunity->toLocalizedArray(['slug', 'title', 'sector', 'summary'])),
            'projects' => CouncilProject::query()->with('translations')->whereIn('status', ['draft', 'published'])->orderBy('display_order')->limit(3)->get(['id', 'slug', 'title', 'project_status', 'summary'])->map(fn (CouncilProject $project): array => $project->toLocalizedArray(['slug', 'title', 'project_status', 'summary'])),
            'slides' => HomepageSlide::query()->with('translations')->whereIn('status', ['draft', 'published'])->where('is_active', true)->orderBy('display_order')->get(['id', 'headline', 'supporting_text', 'cta_label', 'cta_url', 'media_id'])->map(function (HomepageSlide $slide): array {
                $media = $slide->media_id ? Media::query()->where('id', $slide->media_id)->where('status', 'active')->first() : null;

                return [...$slide->toLocalizedArray(['headline', 'supporting_text', 'cta_label', 'cta_url']), 'image_url' => $media ? public_route('managed-media.show', $media->id) : null, 'image_alt' => $media?->translated('alt_text'), 'image_caption' => $media?->translated('caption')];
            }),
            'meetings' => CouncilMeeting::query()->with('translations')->whereIn('status', ['draft', 'published'])->where('meeting_status', 'scheduled')->orderBy('scheduled_date')->orderBy('display_order')->limit(3)->get(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue'])->map(fn (CouncilMeeting $meeting): array => $meeting->toLocalizedArray(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue'])),
        ]);
    }

    public function page(string $locale, string $slug): Response
    {
        $this->authorizePreview();

        $page = Page::query()->with('translations')->where('slug', $slug)->firstOrFail();

        return Inertia::render('CmsPage', [
            'preview' => true,
            'page' => [...$page->toLocalizedArray(['slug', 'title', 'summary', 'blocks', 'seo_title', 'meta_description']), 'is_review_content' => true],
        ]);
    }

    /**
     * Draft detail pages for the stakeholder preview.
     */
    public function detail(string $locale, string $type, string $slug): Response
    {
        $this->authorizePreview();

        return match ($type) {
            'services' => $this->previewService($slug),
            'news', 'notices' => $this->previewEditorial($type, $slug),
            'documents' => $this->previewDocument($slug),
            'wards' => $this->previewWard($slug),
            'officials' => $this->previewOfficial($slug),
            'investment' => $this->previewInvestment($slug),
            'meetings' => $this->previewMeeting($slug),
            'galleries' => $this->previewGallery($slug),
            default => abort(404),
        };
    }

    private function authorizePreview(): void
    {
        abort_unless(session('preview_authorized') === true || request()->hasValidSignature(), 403);
    }

    private function previewService(string $slug): Response
    {
        $service = Service::query()->with('translations')->where('slug', $slug)->firstOrFail();

        return Inertia::render('Service', [
            'preview' => true,
            'service' => [...$service->toLocalizedArray(['slug', 'name', 'summary', 'description', 'requirements', 'steps', 'fees_information', 'seo_title', 'meta_description']), 'is_review_content' => true],
            'department' => null,
            'documents' => [],
        ]);
    }

    private function previewEditorial(string $type, string $slug): Response
    {
        $expected = $type === 'news' ? 'news' : 'notice';
        $item = EditorialItem::query()->with('translations')->where('type', $expected)->where('slug', $slug)->firstOrFail();

        return Inertia::render('EditorialDetail', [
            'preview' => true,
            'type' => $type === 'news' ? 'News' : 'Notice',
            'item' => [...$item->toLocalizedArray(['slug', 'title', 'summary', 'body', 'published_at']), 'image_url' => null, 'is_review_content' => true],
            'documents' => [],
        ]);
    }

    private function previewDocument(string $slug): Response
    {
        $document = Document::query()->with('translations')->where('slug', $slug)->firstOrFail();

        return Inertia::render('Document', [
            'preview' => true,
            'document' => [...$document->toLocalizedArray(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count', 'current_version']), 'reference_year' => $document->referenceYear(), 'has_file' => $document->media instanceof Media && Storage::disk(config('cms.media_disk'))->exists($document->media->storage_path), 'is_review_content' => true],
        ]);
    }

    private function previewWard(string $slug): Response
    {
        $ward = Ward::query()->where('slug', $slug)->firstOrFail();

        return Inertia::render('Ward', [
            'preview' => true,
            'ward' => [...$ward->only(['slug', 'name', 'description', 'boundaries_description']), 'is_review_content' => true],
        ]);
    }

    private function previewOfficial(string $slug): Response
    {
        $official = Official::query()->where('slug', $slug)->firstOrFail();

        return Inertia::render('Official', [
            'preview' => true,
            'official' => [...$official->only(['slug', 'name', 'title', 'biography']), 'department' => null, 'photo_url' => null, 'is_review_content' => true],
        ]);
    }

    private function previewInvestment(string $slug): Response
    {
        $opportunity = InvestmentOpportunity::query()->with('translations')->where('slug', $slug)->firstOrFail();

        return Inertia::render('InvestmentDetail', [
            'preview' => true,
            'opportunity' => [...$opportunity->toLocalizedArray(['slug', 'title', 'sector', 'summary', 'description', 'location', 'opportunity_status']), 'document' => null, 'is_review_content' => true],
        ]);
    }

    private function previewMeeting(string $slug): Response
    {
        $meeting = CouncilMeeting::query()->with('translations')->where('id', $slug)->firstOrFail();

        return Inertia::render('Meeting', [
            'preview' => true,
            'meeting' => [...$meeting->toLocalizedArray(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status', 'summary']), 'agenda' => null, 'minutes' => null],
        ]);
    }

    private function previewGallery(string $slug): Response
    {
        $gallery = Gallery::query()->with(['translations', 'media.translations', 'media', 'department.translations', 'department'])->where('slug', $slug)->firstOrFail();
        $images = $gallery->media->where('status', 'active')->values()->map(fn (Media $media): array => [
            'url' => public_route('managed-media.show', $media->id),
            'alt' => $media->translated('alt_text') ?: $gallery->title,
            'caption' => $media->pivot->caption ?: $media->translated('caption'),
            'width' => $media->width,
            'height' => $media->height,
        ]);

        return Inertia::render('Gallery', [
            'preview' => true,
            'gallery' => [
                ...$gallery->toLocalizedArray(['slug', 'title', 'summary', 'description', 'location']),
                'album_date' => $gallery->album_date?->format('Y-m-d'),
                'department' => $gallery->department?->translated('public_name'),
                'is_review_content' => true,
            ],
            'images' => $images->values(),
        ]);
    }
}
