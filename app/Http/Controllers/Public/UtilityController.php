<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UtilityController extends Controller
{
    public function comingSoon(): Response
    {
        $topics = ['council', 'media', 'engagement', 'search', 'tenders', 'vacancies', 'news', 'events', 'services', 'projects', 'tourism', 'contact', 'privacy', 'terms', 'accessibility', 'water', 'roads', 'health', 'environment', 'development', 'community'];
        $topic = request()->query('topic');

        return Inertia::render('ComingSoon', [
            'topic' => is_string($topic) && in_array($topic, $topics, true) ? ucfirst($topic) : 'This section',
        ]);
    }

    public function downloads(): RedirectResponse
    {
        return redirect(public_route('documents.index'));
    }
}
