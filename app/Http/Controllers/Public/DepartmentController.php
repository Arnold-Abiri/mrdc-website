<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Official;
use App\Models\PublicContact;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(): Response
    {
        $departments = Department::query()->with('translations')->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary']);

        return Inertia::render('PublicDepartments', ['departments' => $departments->map(fn (Department $department): array => $department->toLocalizedArray(['id', 'public_name', 'public_summary']))]);
    }

    public function show(string $locale, Department $department): Response
    {
        abort_unless($department->status === 'active' && $department->public_status === 'published' && $department->public_verification_status === 'publishable' && $department->public_published_at && now()->gte($department->public_published_at), 404);
        $head = Official::query()->with('translations')->public()->where('department_id', $department->id)->where('is_department_head', true)->orderBy('display_order')->first(['id', 'slug', 'name', 'title']);
        $officials = Official::query()->with('translations')->public()->where('department_id', $department->id)->where('is_department_head', false)->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'title']);
        $services = Service::query()->with('translations')->public()->where('department_id', $department->id)->orderBy('display_order')->orderBy('name')->get(['id', 'slug', 'name', 'summary']);
        $contacts = PublicContact::query()->public()->where('department_id', $department->id)->orderBy('display_order')->get(['office', 'type', 'value']);
        $documents = Document::query()->with('translations')->public()->where('department_id', $department->id)->orderByDesc('published_at')->limit(10)->get(['id', 'slug', 'title', 'category']);
        $news = EditorialItem::query()->with('translations')->public()->where('type', 'news')->where('department_id', $department->id)->orderByDesc('published_at')->limit(5)->get(['id', 'slug', 'title']);
        $notices = EditorialItem::query()->with('translations')->public()->where('type', 'notice')->where('department_id', $department->id)->orderByDesc('published_at')->limit(5)->get(['id', 'slug', 'title']);

        return Inertia::render('PublicDepartment', ['department' => $department->toLocalizedArray(['public_name', 'public_summary', 'public_description', 'responsibilities']), 'head' => $head?->toLocalizedArray(['slug', 'name', 'title']), 'officials' => $officials->map(fn (Official $official): array => $official->toLocalizedArray(['slug', 'name', 'title'])), 'services' => $services->map(fn (Service $service): array => $service->toLocalizedArray(['slug', 'name', 'summary'])), 'contacts' => $contacts, 'documents' => $documents->map(fn (Document $document): array => $document->toLocalizedArray(['slug', 'title', 'category'])), 'news' => $news->map(fn (EditorialItem $item): array => $item->toLocalizedArray(['slug', 'title'])), 'notices' => $notices->map(fn (EditorialItem $item): array => $item->toLocalizedArray(['slug', 'title']))]);
    }
}
