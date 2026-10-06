<?php

namespace App\Http\Controllers;

use App\Domain\Cms\EnquiryManager;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Feedback', [
            'csrfToken' => csrf_token(),
            'submitted' => request()->query('submitted') === '1',
            'reference' => is_string(request()->query('reference')) ? request()->query('reference') : null,
            'departments' => Department::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, EnquiryManager $manager): RedirectResponse
    {
        $enquiry = $manager->submit([...$request->all(), 'consent_given' => $request->boolean('consent_given')]);

        return redirect()->route('feedback.create', ['locale' => app()->getLocale(), 'submitted' => '1', 'reference' => substr($enquiry->public_id, 0, 8)]);
    }
}
