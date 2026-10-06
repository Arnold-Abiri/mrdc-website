<?php

namespace App\Http\Controllers\Public;

use App\Models\Document;

use App\Models\CouncilMeeting;
use Inertia\Inertia;

class MeetingController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Meetings', ['meetings' => CouncilMeeting::query()->with('translations')->public()->orderBy('scheduled_date')->orderBy('display_order')->get(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status'])]);
    }

    public function show(string $locale, CouncilMeeting $meeting): \Inertia\Response
    {
        abort_unless($meeting->status === 'published' && $meeting->verification_status === 'publishable' && $meeting->published_at && now()->gte($meeting->published_at), 404);
                $meeting->load(['agenda', 'minutes']);
                $agenda = $meeting->agenda;
                $minutes = $meeting->minutes;

                return Inertia::render('Meeting', ['meeting' => [...$meeting->toLocalizedArray(['id', 'title', 'meeting_type', 'scheduled_date', 'scheduled_time', 'venue', 'meeting_status', 'summary']), 'agenda' => $agenda instanceof Document && $agenda->status === 'published' && $agenda->visibility === 'public' ? $agenda->toLocalizedArray(['slug', 'title']) : null, 'minutes' => $minutes instanceof Document && $minutes->status === 'published' && $minutes->visibility === 'public' ? $minutes->toLocalizedArray(['slug', 'title']) : null]]);
    }

}