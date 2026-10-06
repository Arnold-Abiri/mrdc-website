<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Models\CouncilProject;
use App\Models\Media;
use App\Models\ProjectUpdate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectUpdateManager
{
    public function create(User $actor, array $input): ProjectUpdate
    {
        Gate::forUser($actor)->authorize('create', ProjectUpdate::class);
        $data = $this->validate($input);
        $project = CouncilProject::query()->findOrFail($data['council_project_id']);
        Gate::forUser($actor)->authorize('update', $project);
        $this->authorizeMedia($actor, $data);

        return DB::transaction(function () use ($actor, $data): ProjectUpdate {
            $update = new ProjectUpdate($data);
            $update->created_by = $actor->id;
            $update->updated_by = $actor->id;
            $update->save();
            app(AuditWriter::class)->record($actor, 'project_update.created', $update, ['project_id' => $update->council_project_id]);

            return $update;
        });
    }

    public function update(User $actor, ProjectUpdate $update, array $input): ProjectUpdate
    {
        Gate::forUser($actor)->authorize('update', $update);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $update, $data): ProjectUpdate {
            $update->fill($data);
            $update->status = 'draft';
            $update->published_at = null;
            $update->updated_by = $actor->id;
            $update->save();
            app(AuditWriter::class)->record($actor, 'project_update.updated', $update, ['project_id' => $update->council_project_id]);

            return $update;
        });
    }

    public function setStatus(User $actor, ProjectUpdate $update, string $status): ProjectUpdate
    {
        Gate::forUser($actor)->authorize('update', $update);
        abort_unless(in_array($status, ['published', 'unpublished'], true), 422);

        return DB::transaction(function () use ($actor, $update, $status): ProjectUpdate {
            $update->status = $status;
            $update->published_at = $status === 'published' ? now() : null;
            $update->updated_by = $actor->id;
            $update->save();
            app(AuditWriter::class)->record($actor, 'project_update.'.$status, $update, ['project_id' => $update->council_project_id]);

            return $update;
        });
    }

    private function authorizeMedia(User $actor, array $data): void
    {
        if (! empty($data['media_id'])) {
            $media = Media::query()->findOrFail($data['media_id']);
            Gate::forUser($actor)->authorize('view', $media);
            abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 422);
        }
    }

    private function validate(array $input): array
    {
        return Validator::make($input, [
            'council_project_id' => ['required', 'integer', Rule::exists('council_projects', 'id')],
            'update_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
