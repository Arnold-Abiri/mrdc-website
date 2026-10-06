<?php

namespace App\Http\Controllers\Public;

use App\Models\InvestmentOpportunity;
use App\Models\Page;
use Inertia\Inertia;

class TourismController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        $page = Page::query()->with('translations')->public()->where('slug', 'tourism-mutoko')->first();
                $opportunities = InvestmentOpportunity::query()->with('translations')->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'sector', 'summary']);

                return Inertia::render('Tourism', ['page' => $page ? $page->toLocalizedArray(['slug', 'title', 'summary', 'blocks']) : null, 'opportunities' => $opportunities]);
    }

}