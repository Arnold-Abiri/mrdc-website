<?php

namespace App\Http\Controllers\Public;

use App\Models\Ward;
use Inertia\Inertia;

class WardController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Wards', ['wards' => Ward::query()->public()->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'description'])]);
    }

    public function show(string $locale, string $slug): \Inertia\Response
    {
        $ward = Ward::query()->public()->where('slug', $slug)->firstOrFail();

                return Inertia::render('Ward', ['ward' => $ward->only(['slug', 'name', 'description', 'boundaries_description'])]);
    }

}