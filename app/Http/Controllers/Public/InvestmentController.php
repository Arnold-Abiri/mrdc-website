<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\InvestmentOpportunity;
use Inertia\Inertia;
use Inertia\Response;

class InvestmentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Investment', ['opportunities' => InvestmentOpportunity::query()->with('translations')->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'sector', 'summary', 'location', 'opportunity_status'])]);
    }

    public function show(string $locale, string $slug): Response
    {
        $opportunity = InvestmentOpportunity::query()->with('translations')->public()->with('document')->where('slug', $slug)->firstOrFail();
        $document = $opportunity->document;

        return Inertia::render('InvestmentDetail', ['opportunity' => [...$opportunity->toLocalizedArray(['slug', 'title', 'sector', 'summary', 'description', 'location', 'opportunity_status']), 'document' => $document instanceof Document && $document->status === 'published' ? $document->toLocalizedArray(['slug', 'title']) : null]]);
    }
}
