<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\CouncilProject;
use App\Models\Document;
use App\Models\Media;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouncilProjectManager
{
    public function create(User $actor, array $input): CouncilProject
    {
        Gate::forUser($actor)->authorize('create', CouncilProject::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'projects.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $data): CouncilProject {
            $wards = $data['wards'] ?? [];
            $documents = $data['documents'] ?? [];
            unset($data['wards'], $data['documents']);
            $project = new CouncilProject($data);
            $project->created_by = $actor->id;
            $project->updated_by = $actor->id;
            $project->save();
            $project->wards()->sync($wards);
            $project->documents()->sync($documents);
            app(AuditWriter::class)->record($actor, 'project.created', $project, ['slug' => $project->slug]);

            return $project;
        });
    }

    public function update(User $actor, CouncilProject $project, array $input): CouncilProject
    {
        Gate::forUser($actor)->authorize('update', $project);
        $data = $this->validate($input, $project);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'projects.update', $data['department_id'] ?? null, $project->created_by), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $project, $data): CouncilProject {
            $project = CouncilProject::query()->lockForUpdate()->findOrFail($project->id);
            Gate::forUser($actor)->authorize('update', $project);
            $wards = $data['wards'] ?? [];
            $documents = $data['documents'] ?? [];
            unset($data['wards'], $data['documents']);
            $project->fill($data);
            $project->status = 'draft';
            $project->verification_status = 'demo';
            $project->published_at = null;
            $project->verified_by = null;
            $project->verified_at = null;
            $project->updated_by = $actor->id;
            $project->save();
            $project->wards()->sync($wards);
            $project->documents()->sync($documents);
            app(AuditWriter::class)->record($actor, 'project.updated', $project, ['slug' => $project->slug]);

            return $project;
        });
    }

    public function setVerification(User $actor, CouncilProject $project, string $state): CouncilProject
    {
        Gate::forUser($actor)->authorize('verify', $project);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $project, $state): CouncilProject {
            $project = CouncilProject::query()->lockForUpdate()->findOrFail($project->id);
            Gate::forUser($actor)->authorize('verify', $project);
            $project->verification_status = $state;
            $project->verified_by = $state === 'demo' ? null : $actor->id;
            $project->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $project->status = 'unpublished';
                $project->published_at = null;
            }
            $project->updated_by = $actor->id;
            $project->save();
            app(AuditWriter::class)->record($actor, 'project.verification_changed', $project, ['state' => $state]);

            return $project;
        });
    }

    public function setStatus(User $actor, CouncilProject $project, string $status): CouncilProject
    {
        Gate::forUser($actor)->authorize('publish', $project);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $project, $status): CouncilProject {
            $project = CouncilProject::query()->lockForUpdate()->findOrFail($project->id);
            Gate::forUser($actor)->authorize('publish', $project);
            abort_if($status === 'published' && $project->verification_status !== 'publishable', 422);
            $project->status = $status;
            $project->published_at = $status === 'published' ? now() : null;
            $project->updated_by = $actor->id;
            $project->save();
            app(AuditWriter::class)->record($actor, 'project.'.$status, $project, ['slug' => $project->slug]);

            return $project;
        });
    }

    private function authorizeRelations(User $actor, array $data): void
    {
        if (! empty($data['featured_media_id'])) {
            $media = Media::query()->findOrFail($data['featured_media_id']);
            Gate::forUser($actor)->authorize('view', $media);
            abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 422);
        }
        foreach ($data['wards'] ?? [] as $id) {
            Ward::query()->findOrFail($id);
        }
        foreach ($data['documents'] ?? [] as $id) {
            $document = Document::query()->findOrFail($id);
            Gate::forUser($actor)->authorize('view', $document);
        }
    }

    private function validate(array $input, ?CouncilProject $project = null): array
    {
        return Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('council_projects', 'slug')->ignore($project?->id)],
            'project_type' => ['required', Rule::in(['project', 'programme'])],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'location' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'description' => ['required', 'string', 'max:50000'],
            'starts_at' => ['nullable', 'date'],
            'expected_completed_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'project_status' => ['required', Rule::in(['planned', 'ongoing', 'completed', 'on_hold', 'cancelled'])],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'featured_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'contact_instructions' => ['nullable', 'string', 'max:5000'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'wards' => ['nullable', 'array', 'max:40'], 'wards.*' => ['integer', 'distinct', Rule::exists('wards', 'id')],
            'documents' => ['nullable', 'array', 'max:20'], 'documents.*' => ['integer', 'distinct', Rule::exists('documents', 'id')],
        ])->validate();
    }
}
