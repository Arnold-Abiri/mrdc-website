<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilMeeting;
use App\Models\Document;
use Inertia\Inertia;
use Inertia\Response;

class MeetingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Meetings', ['meetings' => CouncilMeeting::query()->with('translations')->public()->orderBy('scheduled_date')->orderBy('display_order')->get(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status'])]);
    }

    public function show(string $locale, CouncilMeeting $meeting): Response
    {
        abort_unless($meeting->status === 'published' && $meeting->verification_status === 'publishable' && $meeting->published_at && now()->gte($meeting->published_at), 404);
        $meeting->load(['agenda', 'minutes']);
        $agenda = $meeting->agenda;
        $minutes = $meeting->minutes;

        return Inertia::render('Meeting', ['meeting' => [...$meeting->toLocalizedArray(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status', 'summary']), 'agenda' => $agenda instanceof Document && $agenda->status === 'published' && $agenda->visibility === 'public' ? $agenda->toLocalizedArray(['slug', 'title']) : null, 'minutes' => $minutes instanceof Document && $minutes->status === 'published' && $minutes->visibility === 'public' ? $minutes->toLocalizedArray(['slug', 'title']) : null]]);
    }
}
