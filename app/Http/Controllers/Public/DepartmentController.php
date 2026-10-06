<?php

namespace App\Http\Controllers\Public;

use App\Models\Department;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Official;
use App\Models\PublicContact;
use App\Models\Service;
use Inertia\Inertia;

class DepartmentController extends \App\Http\Controllers\Controller
{
    public function index(): \Inertia\Response
    {
        $departments = Department::query()->with('translations')->where('status', 'active')->where('public_status', 'published')->where('public_verification_status', 'publishable')->whereNotNull('public_published_at')->where('public_published_at', '<=', now())->orderBy('public_display_order')->get(['id', 'public_name', 'public_summary']);

                return Inertia::render('PublicDepartments', ['departments' => $departments]);
    }

    public function show(string $locale, Department $department): \Inertia\Response
    {
        abort_unless($department->status === 'active' && $department->public_status === 'published' && $department->public_verification_status === 'publishable' && $department->public_published_at && now()->gte($department->public_published_at), 404);
                $head = Official::query()->public()->where('department_id', $department->id)->where('is_department_head', true)->orderBy('display_order')->first(['slug', 'name', 'title']);
                $officials = Official::query()->public()->where('department_id', $department->id)->where('is_department_head', false)->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'title']);
                $services = Service::query()->with('translations')->public()->where('department_id', $department->id)->orderBy('display_order')->orderBy('name')->get(['slug', 'name', 'summary']);
                $contacts = PublicContact::query()->public()->where('department_id', $department->id)->orderBy('display_order')->get(['office', 'type', 'value']);
                $documents = Document::query()->with('translations')->public()->where('department_id', $department->id)->orderByDesc('published_at')->limit(10)->get(['slug', 'title', 'category']);
                $news = EditorialItem::query()->with('translations')->public()->where('type', 'news')->where('department_id', $department->id)->orderByDesc('published_at')->limit(5)->get(['slug', 'title']);
                $notices = EditorialItem::query()->with('translations')->public()->where('type', 'notice')->where('department_id', $department->id)->orderByDesc('published_at')->limit(5)->get(['slug', 'title']);

                return Inertia::render('PublicDepartment', ['department' => $department->toLocalizedArray(['public_name', 'public_summary', 'public_description', 'responsibilities']), 'head' => $head, 'officials' => $officials, 'services' => $services, 'contacts' => $contacts, 'documents' => $documents, 'news' => $news, 'notices' => $notices]);
    }

}