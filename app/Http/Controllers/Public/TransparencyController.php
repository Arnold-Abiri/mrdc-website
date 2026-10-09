<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransparencyController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->query('category');
        $year = $request->query('year');
        abort_unless($category === null || in_array($category, Document::FINANCIAL_CATEGORIES, true), 422);
        abort_unless($year === null || (is_numeric($year) && (int) $year >= 1990 && (int) $year <= (int) now()->format('Y') + 1), 422);
        $documents = Document::query()->with('translations')->public()->whereIn('category', Document::FINANCIAL_CATEGORIES)
            ->when($category, fn ($query) => $query->where('category', $category))
            ->when($year, fn ($query) => $query->whereYear('reference_date', (int) $year))
            ->orderByDesc('published_at')->get(['slug', 'title', 'description', 'category', 'published_at', 'reference_date', 'download_count']);
        $years = Document::query()->with('translations')->public()->whereIn('category', Document::FINANCIAL_CATEGORIES)->whereNotNull('reference_date')->orderByDesc('reference_date')->pluck('reference_date')->map(fn ($date): int => (int) substr((string) $date, 0, 4))->unique()->values();

        return Inertia::render('Transparency', ['documents' => $documents, 'categories' => Document::FINANCIAL_CATEGORIES, 'years' => $years, 'filters' => ['category' => $category, 'year' => $year]]);
    }
}
