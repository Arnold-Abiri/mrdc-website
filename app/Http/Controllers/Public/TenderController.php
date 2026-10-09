<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Document;
use App\Models\Tender;
use Inertia\Inertia;
use Inertia\Response;

class TenderController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tenders', ['tenders' => Tender::query()->with('translations')->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'reference', 'title', 'category', 'closes_at'])->map(fn (Tender $tender): array => [...$tender->toLocalizedArray(['slug', 'reference', 'title', 'category', 'closes_at']), 'display_status' => $tender->displayStatus()])]);
    }

    public function show(string $locale, string $slug): Response
    {
        $tender = Tender::query()->with('translations')->public()->with(['document', 'department'])->where('slug', $slug)->firstOrFail();
        $document = $tender->document;

        return Inertia::render('Tender', ['tender' => [...$tender->toLocalizedArray(['slug', 'reference', 'title', 'category', 'description', 'opens_at', 'closes_at', 'contact_instructions', 'award_status', 'awarded_to', 'awarded_at', 'award_amount', 'award_reference', 'award_remarks']), 'display_status' => $tender->displayStatus(), 'document' => $document instanceof Document && $document->status === 'published' ? $document->toLocalizedArray(['slug', 'title']) : null], 'department' => $tender->department instanceof Department ? $tender->department->name : null]);
    }
}
