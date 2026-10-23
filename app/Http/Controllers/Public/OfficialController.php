<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilMeeting;
use App\Models\Department;
use App\Models\Official;
use Inertia\Inertia;
use Inertia\Response;

class OfficialController extends Controller
{
    public function index(): Response
    {
        $officials = Official::query()->public()->with(['department.translations', 'translations'])->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'title', 'biography', 'department_id', 'photo_media_id']);

        return Inertia::render('Officials', ['officials' => $officials->map(fn (Official $official): array => [...$official->toLocalizedArray(['slug', 'name', 'title', 'biography']), 'department' => $official->department instanceof Department ? $official->department->translated('public_name') : null, 'photo_url' => $official->photo_media_id ? public_route('managed-media.show', $official->photo_media_id) : null])]);
    }

    public function chairperson(string $locale): Response
    {
        $chairperson = Official::query()->public()
            ->with(['translations'])
            ->where(function ($q) {
                $q->where('slug', 'alderman-trust-kachidza')
                    ->orWhere('title', 'like', '%Chairperson%');
            })
            ->orderBy('display_order')
            ->first();

        if (! $chairperson) {
            $chairperson = Official::query()->firstOrNew(['slug' => 'alderman-trust-kachidza']);
            $chairperson->fill([
                'name' => 'Alderman Trust Kachidza',
                'title' => 'Council Chairperson',
                'biography' => 'Alderman Trust Kachidza is the Council Chairperson of Mutoko Rural District Council. As the elected political head of the local authority under the Rural District Councils Act [Chapter 29:13], he presides over Full Council meetings and spearheads the policy direction, legislative oversight, and devolved community development agenda across all 29 wards of Mutoko District.',
            ]);
        }

        $upcomingMeetings = CouncilMeeting::query()->public()
            ->where('meeting_type', 'full_council')
            ->where('meeting_status', 'scheduled')
            ->orderBy('scheduled_date')
            ->limit(3)
            ->get();

        return Inertia::render('Chairperson', [
            'chairperson' => [
                ...$chairperson->toLocalizedArray(['slug', 'name', 'title', 'biography']),
                'photo_url' => $chairperson->photo_media_id ? public_route('managed-media.show', $chairperson->photo_media_id) : null,
            ],
            'upcoming_meetings' => $upcomingMeetings->map(fn ($m) => [
                'id' => $m->id,
                'title' => $m->translated('title'),
                'scheduled_date' => $m->scheduled_date?->toDateString(),
                'scheduled_time' => $m->scheduled_time,
                'venue' => $m->translated('venue'),
            ]),
        ]);
    }

    public function show(string $locale, string $slug): Response
    {
        $official = Official::query()->public()->with(['department.translations', 'translations'])->where('slug', $slug)->firstOrFail();

        return Inertia::render('Official', ['official' => [...$official->toLocalizedArray(['slug', 'name', 'title', 'biography']), 'department' => $official->department instanceof Department ? $official->department->translated('public_name') : null, 'photo_url' => $official->photo_media_id ? public_route('managed-media.show', $official->photo_media_id) : null]]);
    }
}
