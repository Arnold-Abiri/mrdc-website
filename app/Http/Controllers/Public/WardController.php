<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use Inertia\Inertia;
use Inertia\Response;

class WardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Wards', ['wards' => Ward::query()->with('translations')->public()->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'description'])->map(fn (Ward $ward): array => $ward->toLocalizedArray(['slug', 'name', 'description']))]);
    }

    public function show(string $locale, string $slug): Response
    {
        $ward = Ward::query()->with('translations')->public()->where('slug', $slug)->firstOrFail();

        return Inertia::render('Ward', ['ward' => $ward->toLocalizedArray(['slug', 'name', 'description', 'boundaries_description'])]);
    }
}
