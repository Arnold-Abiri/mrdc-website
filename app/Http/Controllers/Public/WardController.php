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
        return Inertia::render('Wards', ['wards' => Ward::query()->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'description'])]);
    }

    public function show(string $locale, string $slug): Response
    {
        $ward = Ward::query()->public()->where('slug', $slug)->firstOrFail();

        return Inertia::render('Ward', ['ward' => $ward->only(['slug', 'name', 'description', 'boundaries_description'])]);
    }
}
