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
            'services' => Service::query()->with('translations')->where('status', 'draft')->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary']),
            'documents' => Document::query()->with('translations')->where('status', 'draft')->orderBy('title')->limit(5)->get(['slug', 'title', 'description']),
            'news' => EditorialItem::query()->with('translations')->where('type', 'news')->where('status', 'draft')->orderBy('display_order')->limit(3)->get(['slug', 'title', 'summary', 'published_at']),
            'notices' => EditorialItem::query()->with('translations')->where('type', 'notice')->where('status', 'draft')->orderBy('display_order')->limit(3)->get(['slug', 'title', 'summary', 'published_at']),
            'departments' => Department::query()->with('translations')->where('status', 'active')->orderBy('public_display_order')->limit(4)->get(['id', 'public_name', 'public_summary']),
            'contacts' => PublicContact::query()->orderBy('display_order')->limit(4)->get(['office', 'type', 'value']),
            'officials' => Official::query()->where('status', 'draft')->orderBy('display_order')->limit(3)->get(['slug', 'name', 'title']),
            'ward_count' => Ward::query()->where('status', 'draft')->count(),
            'statistics' => DistrictStatistic::query()->public()->orderBy('display_order')->get(['label', 'value', 'unit', 'icon']),
            'tenders' => Tender::query()->with('translations')->where('status', 'draft')->orderBy('display_order')->limit(3)->get(['slug', 'reference', 'title'])->map(fn (Tender $tender): array => [...$tender->toLocalizedArray(['slug', 'reference', 'title']), 'display_status' => $tender->displayStatus()]),
            'investment' => InvestmentOpportunity::query()->with('translations')->where('status', 'draft')->orderBy('display_order')->limit(3)->get(['slug', 'title', 'sector', 'summary']),
            'projects' => CouncilProject::query()->with('translations')->where('status', 'draft')->orderBy('display_order')->limit(3)->get(['slug', 'title', 'project_status', 'summary']),
            'slides' => HomepageSlide::query()->where('status', 'draft')->where('is_active', true)->orderBy('display_order')->get(['headline', 'supporting_text', 'cta_label', 'cta_url'])->map(function (HomepageSlide $slide): array {
                $media = $slide->media_id ? Media::query()->where('id', $slide->media_id)->where('status', 'active')->first() : null;

                return [...$slide->only(['headline', 'supporting_text', 'cta_label', 'cta_url']), 'image_url' => $media ? public_route('managed-media.show', $media->id) : null];
            }),
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
}
