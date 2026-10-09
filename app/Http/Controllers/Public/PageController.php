<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use App\Models\Official;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(): Response
    {
        $page = Page::query()->with('translations')->public()->where('verification_status', 'publishable')->where('slug', 'about-mutoko')->first();

        return Inertia::render('About', [
            'page' => $page
                ? [...$page->toLocalizedArray(['title', 'summary', 'blocks', 'seo_title', 'meta_description']), 'is_review_content' => false]
                : ['title' => 'About Mutoko Rural District Council', 'summary' => null, 'blocks' => [], 'seo_title' => null, 'meta_description' => null, 'is_review_content' => false, 'content_pending' => true],
            'about' => $this->aboutDirectory(),
        ]);
    }

    public function show(string $locale, string $slug): Response
    {
        $page = Page::query()->with('translations')->public()->when($slug === 'about-mutoko', fn ($query) => $query->where('verification_status', 'publishable'))->where('slug', $slug)->firstOrFail();

        return Inertia::render('CmsPage', [
            'page' => [...$page->toLocalizedArray(['slug', 'title', 'summary', 'blocks', 'seo_title', 'meta_description']), 'is_review_content' => $page->verification_status !== 'publishable'],
            'about' => $slug === 'about-mutoko' ? $this->aboutDirectory() : null,
        ]);
    }

    private function aboutDirectory(): array
    {
        return [
            'officials' => Official::query()->public()->orderBy('display_order')->limit(4)->get(['slug', 'name', 'title', 'photo_media_id'])->map(fn ($official): array => [
                'slug' => $official->slug,
                'name' => $official->name,
                'title' => $official->title,
                'photo_url' => $official->photo_media_id ? public_route('managed-media.show', $official->photo_media_id) : null,
            ]),
            'investment' => InvestmentOpportunity::query()->public()->orderBy('display_order')->limit(3)->get(['slug', 'title', 'summary']),
            'pages' => Page::query()->public()->where('verification_status', 'publishable')->whereIn('slug', ['mandate', 'organogram'])->pluck('slug'),
        ];
    }
}
