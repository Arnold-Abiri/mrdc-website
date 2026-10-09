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
        return Inertia::render('Services', ['services' => Service::query()->with('translations')->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary'])]);
    }

    public function show(string $locale, string $slug): Response
    {
        $service = Service::query()->with('translations')->public()->with('department')->where('slug', $slug)->firstOrFail();

        $department = $service->department;

        return Inertia::render('Service', ['service' => $service->toLocalizedArray(['slug', 'name', 'summary', 'description', 'requirements', 'steps', 'fees_information', 'seo_title', 'meta_description']), 'department' => $department instanceof Department ? $department->name : null, 'documents' => $service->department_id ? Document::query()->with('translations')->public()->where('department_id', $service->department_id)->orderByDesc('published_at')->limit(10)->get(['slug', 'title', 'category']) : []]);
    }
}
