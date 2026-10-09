<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Official;
use Inertia\Inertia;
use Inertia\Response;

class OfficialController extends Controller
{
    public function index(): Response
    {
        $officials = Official::query()->public()->with('department')->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'title', 'biography', 'department_id', 'photo_media_id']);

        return Inertia::render('Officials', ['officials' => $officials->map(fn (Official $official): array => [...$official->only(['slug', 'name', 'title', 'biography']), 'department' => $official->department instanceof Department ? $official->department->name : null, 'photo_url' => $official->photo_media_id ? public_route('managed-media.show', $official->photo_media_id) : null])]);
    }

    public function show(string $locale, string $slug): Response
    {
        $official = Official::query()->public()->with('department')->where('slug', $slug)->firstOrFail();

        return Inertia::render('Official', ['official' => [...$official->only(['slug', 'name', 'title', 'biography']), 'department' => $official->department instanceof Department ? $official->department->name : null, 'photo_url' => $official->photo_media_id ? public_route('managed-media.show', $official->photo_media_id) : null]]);
    }
}
