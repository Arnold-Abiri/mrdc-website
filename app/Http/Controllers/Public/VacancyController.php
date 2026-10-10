<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Document;
use App\Models\Vacancy;
use Inertia\Inertia;
use Inertia\Response;

class VacancyController extends Controller
{
    public function index(): Response
    {
        $vacancies = Vacancy::query()->with(['translations', 'department'])->public()->orderBy('display_order')->orderBy('title')->get()->map(fn (Vacancy $vacancy): array => [...$vacancy->toLocalizedArray(['slug', 'title', 'grade', 'reference', 'employment_type', 'description', 'opens_at', 'closes_at']), 'is_open' => $vacancy->isOpen(), 'department' => $vacancy->department instanceof Department ? $vacancy->department->name : null]);

        return Inertia::render('Vacancies', ['vacancies' => $vacancies]);
    }

    public function show(string $locale, string $slug): Response
    {
        $vacancy = Vacancy::query()->with('translations')->public()->with(['document', 'department'])->where('slug', $slug)->firstOrFail();
        $document = $vacancy->document;

        $related = Vacancy::query()->with(['translations', 'department'])->public()->where('id', '!=', $vacancy->id)->orderBy('display_order')->orderBy('title')->limit(3)->get()->map(fn (Vacancy $relatedVacancy): array => [...$relatedVacancy->toLocalizedArray(['slug', 'title', 'grade', 'closes_at']), 'is_open' => $relatedVacancy->isOpen(), 'department' => $relatedVacancy->department instanceof Department ? $relatedVacancy->department->name : null]);

        return Inertia::render('Vacancy', ['vacancy' => [...$vacancy->toLocalizedArray(['slug', 'title', 'grade', 'reference', 'employment_type', 'description', 'responsibilities', 'requirements', 'opens_at', 'closes_at', 'application_instructions']), 'is_open' => $vacancy->isOpen(), 'document' => $document instanceof Document && $document->status === 'published' ? $document->toLocalizedArray(['slug', 'title']) : null], 'department' => $vacancy->department instanceof Department ? $vacancy->department->name : null, 'related' => $related]);
    }
}
