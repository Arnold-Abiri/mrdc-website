<?php

namespace App\Http\Controllers\Public;

use App\Models\Page;
use Inertia\Inertia;

class PageController extends \App\Http\Controllers\Controller
{
    public function show(string $locale, string $slug): \Inertia\Response
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
            'officials' => \App\Models\Official::query()->public()->orderBy('display_order')->limit(4)->get(['slug', 'name', 'title', 'photo_media_id'])->map(fn ($official): array => [
                'slug' => $official->slug,
                'name' => $official->name,
                'title' => $official->title,
                'photo_url' => $official->photo_media_id ? public_route('managed-media.show', $official->photo_media_id) : null,
            ]),
            'investment' => \App\Models\InvestmentOpportunity::query()->public()->orderBy('display_order')->limit(3)->get(['slug', 'title', 'summary']),
        ];
    }
}
