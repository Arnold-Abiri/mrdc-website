<?php

namespace App\Http\Controllers\Public;

use App\Models\Page;
use Inertia\Inertia;

class PageController extends \App\Http\Controllers\Controller
{
    public function show(string $locale, string $slug): \Inertia\Response
    {
        $page = Page::query()->with('translations')->public()->where('slug', $slug)->firstOrFail();

                return Inertia::render('CmsPage', [
                    'page' => [...$page->toLocalizedArray(['slug', 'title', 'summary', 'blocks', 'seo_title', 'meta_description']), 'is_review_content' => $page->verification_status !== 'publishable'],
                ]);
    }

}