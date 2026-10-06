<?php

namespace App\Http\Controllers;

use App\Domain\Cms\EnquiryManager;
use App\Models\InvestmentOpportunity;
use App\Models\PublicContact;
use App\Models\Service;
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
            'context' => $this->resolveContext($request->query('context')),
        ]);
    }

    public function store(Request $request, EnquiryManager $manager): RedirectResponse
    {
        $manager->submit($request->all());

        return redirect()->route('contact.create', ['submitted' => '1']);
    }

    /**
     * @return array{type: string, reference: string, title: string}|null
     */
    private function resolveContext(mixed $raw): ?array
    {
        if (! is_string($raw) || ! str_contains($raw, ':')) {
            return null;
        }
        [$type, $slug] = explode(':', $raw, 2);
        if ($type === 'service') {
            $record = Service::query()->public()->where('slug', $slug)->first(['slug', 'name']);
            if ($record) {
                return ['type' => 'service', 'reference' => $record->slug, 'title' => $record->name];
            }
        }
        if ($type === 'investment') {
            $record = InvestmentOpportunity::query()->public()->where('slug', $slug)->first(['slug', 'title']);
            if ($record) {
                return ['type' => 'investment', 'reference' => $record->slug, 'title' => $record->title];
            }
        }

        return null;
    }
}
