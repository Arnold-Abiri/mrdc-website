<?php

namespace App\Http\Controllers;

use App\Domain\Cms\EnquiryManager;
use App\Models\PublicContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicEnquiryController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Contact', [
            'csrfToken' => csrf_token(),
            'submitted' => $request->query('submitted') === '1',
            'contacts' => PublicContact::query()->public()->orderBy('display_order')->get(['office', 'type', 'value']),
        ]);
    }

    public function store(Request $request, EnquiryManager $manager): RedirectResponse
    {
        $manager->submit($request->all());

        return redirect()->route('contact.create', ['submitted' => '1']);
    }
}
