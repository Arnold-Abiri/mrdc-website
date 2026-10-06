<?php

namespace App\Http\Controllers\Public;

use App\Models\Document;

use App\Models\InvestmentOpportunity;
use Inertia\Inertia;

class InvestmentController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Investment', ['opportunities' => InvestmentOpportunity::query()->with('translations')->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'sector', 'summary', 'location', 'opportunity_status'])]);
    }

    public function show(string $locale, string $slug): \Inertia\Response
    {
        $opportunity = InvestmentOpportunity::query()->with('translations')->public()->with('document')->where('slug', $slug)->firstOrFail();
                $document = $opportunity->document;

                return Inertia::render('InvestmentDetail', ['opportunity' => [...$opportunity->toLocalizedArray(['slug', 'title', 'sector', 'summary', 'description', 'location', 'opportunity_status']), 'document' => $document instanceof Document && $document->status === 'published' ? $document->toLocalizedArray(['slug', 'title']) : null]]);
    }

}