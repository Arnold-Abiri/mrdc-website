<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Document;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Services', ['services' => Service::query()->with('translations')->public()->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'summary'])->map(fn (Service $service): array => $service->toLocalizedArray(['slug', 'name', 'summary']))]);
    }

    public function show(string $locale, string $slug): Response
    {
        $service = Service::query()->with('translations')->public()->with('department.translations', 'department')->where('slug', $slug)->firstOrFail();

        $department = $service->department;

        $relatedServices = Service::query()
            ->with('translations')
            ->public()
            ->where('id', '!=', $service->id)
            ->orderBy('display_order')
            ->limit(4)
            ->get(['id', 'slug', 'name', 'summary'])
            ->map(fn (Service $item): array => $item->toLocalizedArray(['slug', 'name', 'summary']));

        return Inertia::render('Service', [
            'service' => $service->toLocalizedArray(['slug', 'name', 'summary', 'description', 'requirements', 'steps', 'fees_information', 'seo_title', 'meta_description']),
            'department' => $department instanceof Department ? $department->translated('public_name') : null,
            'documents' => $service->department_id ? Document::query()->with('translations')->public()->where('department_id', $service->department_id)->orderByDesc('published_at')->limit(10)->get(['id', 'slug', 'title', 'category'])->map(fn (Document $document): array => $document->toLocalizedArray(['slug', 'title', 'category'])) : [],
            'relatedServices' => $relatedServices,
        ]);
    }
}
