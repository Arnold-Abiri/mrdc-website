<?php

namespace App\Http\Controllers\Public;

use App\Models\Document;
use App\Models\Page;
use Inertia\Inertia;

class RatesController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        $page = Page::query()->with('translations')->public()->where('slug', 'rates-information')->first();
                $schedules = Document::query()->with('translations')->public()->whereIn('category', ['bylaw', 'report', 'plan'])->orderByDesc('published_at')->limit(10)->get(['slug', 'title', 'category', 'reference_date']);

                return Inertia::render('Rates', ['page' => $page ? $page->toLocalizedArray(['slug', 'title', 'summary', 'blocks']) : null, 'schedules' => $schedules]);
    }

}