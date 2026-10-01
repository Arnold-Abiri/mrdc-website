<?php

use App\Models\Page;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');

Route::get('/coming-soon', function () {
    $topics = ['council', 'media', 'engagement', 'search', 'tenders', 'vacancies', 'news', 'events', 'services', 'projects', 'tourism', 'contact', 'privacy', 'terms', 'accessibility', 'water', 'roads', 'health', 'environment', 'development', 'community'];
    $topic = request()->query('topic');

    return Inertia::render('ComingSoon', [
        'topic' => is_string($topic) && in_array($topic, $topics, true) ? ucfirst($topic) : 'This section',
    ]);
})->name('coming-soon');

Route::get('/sitemap.xml', function () {
    $url = htmlspecialchars(route('home'), ENT_XML1, 'UTF-8');

    return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>'.$url.'</loc></url></urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

Route::get('/pages/{slug}', function (string $slug) {
    $page = Page::query()->public()->where('slug', $slug)->firstOrFail();

    return Inertia::render('CmsPage', [
        'page' => [...$page->only(['slug', 'title', 'summary', 'blocks', 'seo_title', 'meta_description']), 'is_review_content' => $page->verification_status !== 'publishable'],
    ]);
})->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('pages.show');
