<?php

namespace App\Http\Controllers;

use App\Domain\Cms\EnquiryManager;
use App\Models\Department;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function create(Request $request): Response
    {
        $rawCategory = $request->query('category') ?? ($request->query('type') === 'fault' ? 'fault_report' : null);
        $initialCategory = is_string($rawCategory) && in_array($rawCategory, EnquiryManager::CITIZEN_CATEGORIES, true) ? $rawCategory : null;
        $initialWard = is_string($request->query('ward')) ? $request->query('ward') : null;

        return Inertia::render('Feedback', [
            'csrfToken' => csrf_token(),
            'submitted' => $request->query('submitted') === '1',
            'reference' => is_string($request->query('reference')) ? $request->query('reference') : null,
            'departments' => Department::query()->where('status', 'active')->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'public_name']),
            'wards' => Ward::query()->public()->orderBy('display_order')->orderBy('name')->get(['id', 'name', 'slug']),
            'initialCategory' => $initialCategory,
            'initialWard' => $initialWard,
        ]);
    }

    public function store(Request $request, EnquiryManager $manager): RedirectResponse
    {
        $payload = $request->all();
        $payload['consent_given'] = $request->boolean('consent_given');

        if ($request->filled('location_detail') && is_string($request->input('location_detail'))) {
            $location = trim($request->input('location_detail'));
            if ($location !== '' && isset($payload['message']) && is_string($payload['message'])) {
                $payload['message'] = "Location / Landmark: {$location}\n\n".$payload['message'];
            }
        }

        $enquiry = $manager->submit($payload);

        return redirect()->route('feedback.create', ['locale' => app()->getLocale(), 'submitted' => '1', 'reference' => substr($enquiry->public_id, 0, 8)]);
    }
}
