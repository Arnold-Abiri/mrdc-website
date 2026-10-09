<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\Document;
use App\Models\Ward;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->query('status');
        $type = $request->query('type');
        $departmentId = $request->query('department');
        $wardId = $request->query('ward');
        abort_unless($status === null || in_array($status, ['planned', 'ongoing', 'completed', 'on_hold', 'cancelled'], true), 422);
        abort_unless($type === null || in_array($type, ['project', 'programme'], true), 422);

        $projects = CouncilProject::query()->with('translations')->public()
            ->when($status, fn ($query) => $query->where('project_status', $status))
            ->when($type, fn ($query) => $query->where('project_type', $type))
            ->when($departmentId, fn ($query) => $query->where('department_id', (int) $departmentId))
            ->when($wardId, fn ($query) => $query->whereHas('wards', fn ($wards) => $wards->where('wards.id', (int) $wardId)))
            ->orderBy('display_order')->orderBy('title')
            ->get(['slug', 'title', 'project_type', 'project_status', 'location', 'summary', 'progress_percent']);

        return Inertia::render('Projects', ['projects' => $projects, 'filters' => ['status' => $status, 'type' => $type, 'department' => $departmentId, 'ward' => $wardId]]);
    }

    public function show(string $locale, string $slug): Response
    {
        $project = CouncilProject::query()->with('translations')->public()->with(['department', 'wards', 'featuredMedia', 'documents' => fn ($query) => $query->public(), 'updates' => fn ($query) => $query->public()->orderByDesc('update_date')])->where('slug', $slug)->firstOrFail();
        $department = $project->department;

        return Inertia::render('Project', ['project' => [...$project->toLocalizedArray(['slug', 'title', 'project_type', 'location', 'summary', 'description', 'starts_at', 'expected_completed_at', 'completed_at', 'project_status', 'progress_percent', 'contact_instructions']), 'image_url' => $project->featured_media_id ? public_route('managed-media.show', $project->featured_media_id) : null], 'department' => $department instanceof Department ? $department->name : null, 'wards' => $project->wards->map(fn (Ward $ward) => $ward->only(['slug', 'name'])), 'documents' => $project->documents->map(fn (Document $document) => $document->toLocalizedArray(['slug', 'title'])), 'updates' => $project->updates->map(fn ($update) => $update->only(['update_date', 'title', 'summary', 'progress_percent']))]);
    }
}
