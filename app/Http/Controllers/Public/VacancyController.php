<?php

namespace App\Http\Controllers\Public;

use App\Models\Department;
use App\Models\Document;

use App\Models\Vacancy;
use Inertia\Inertia;

class VacancyController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Vacancies', ['vacancies' => Vacancy::query()->with('translations')->public()->orderBy('display_order')->orderBy('title')->get(['slug', 'title', 'grade', 'closes_at'])->map(fn (Vacancy $vacancy): array => [...$vacancy->toLocalizedArray(['slug', 'title', 'grade', 'closes_at']), 'is_open' => $vacancy->isOpen()])]);
    }

    public function show(string $locale, string $slug): \Inertia\Response
    {
        $vacancy = Vacancy::query()->with('translations')->public()->with(['document', 'department'])->where('slug', $slug)->firstOrFail();
                $document = $vacancy->document;

                return Inertia::render('Vacancy', ['vacancy' => [...$vacancy->toLocalizedArray(['slug', 'title', 'grade', 'reference', 'employment_type', 'description', 'responsibilities', 'requirements', 'opens_at', 'closes_at', 'application_instructions']), 'is_open' => $vacancy->isOpen(), 'document' => $document instanceof Document && $document->status === 'published' ? $document->toLocalizedArray(['slug', 'title']) : null], 'department' => $vacancy->department instanceof Department ? $vacancy->department->name : null]);
    }

}