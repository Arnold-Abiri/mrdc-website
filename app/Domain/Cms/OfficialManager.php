<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Media;
use App\Models\Official;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OfficialManager
{
    public function create(User $actor, array $input): Official
    {
        Gate::forUser($actor)->authorize('create', Official::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'officials.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizePhoto($actor, $data['photo_media_id'] ?? null);
        $official = new Official($data);
        $official->created_by = $actor->id;
        $official->updated_by = $actor->id;
        $official->save();
        app(AuditWriter::class)->record($actor, 'officials.created', $official);

        return $official;
    }

    public function update(User $actor, Official $official, array $input): Official
    {
        Gate::forUser($actor)->authorize('update', $official);
        $data = $this->validate($input, $official);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'officials.update', $data['department_id'] ?? null, $official->created_by), 403);
        $this->authorizePhoto($actor, $data['photo_media_id'] ?? null);

        return DB::transaction(function () use ($actor, $official, $data): Official {
            $official = Official::query()->lockForUpdate()->findOrFail($official->id);
            Gate::forUser($actor)->authorize('update', $official);
            $official->fill($data);
            $official->status = 'draft';
            $official->verification_status = 'demo';
            $official->published_at = null;
            $official->verified_by = null;
            $official->verified_at = null;
            $official->updated_by = $actor->id;
            $official->save();
            app(AuditWriter::class)->record($actor, 'officials.updated', $official);

            return $official;
        });
    }

    public function setVerification(User $actor, Official $official, string $state): Official
    {
        Gate::forUser($actor)->authorize('verify', $official);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $official, $state): Official {
            $official = Official::query()->lockForUpdate()->findOrFail($official->id);
            Gate::forUser($actor)->authorize('verify', $official);
            $official->verification_status = $state;
            $official->verified_by = $state === 'demo' ? null : $actor->id;
            $official->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $official->status = 'unpublished';
                $official->published_at = null;
            }
            $official->updated_by = $actor->id;
            $official->save();
            app(AuditWriter::class)->record($actor, 'officials.verification_changed', $official, ['state' => $state]);

            return $official;
        });
    }

    public function setStatus(User $actor, Official $official, string $status): Official
    {
        Gate::forUser($actor)->authorize('publish', $official);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $official, $status): Official {
            $official = Official::query()->lockForUpdate()->findOrFail($official->id);
            Gate::forUser($actor)->authorize('publish', $official);
            abort_if($status === 'published' && $official->verification_status !== 'publishable', 422);
            $official->status = $status;
            $official->published_at = $status === 'published' ? now() : null;
            $official->updated_by = $actor->id;
            $official->save();
            app(AuditWriter::class)->record($actor, 'officials.'.$status, $official);

            return $official;
        });
    }

    private function authorizePhoto(User $actor, ?int $mediaId): void
    {
        if ($mediaId === null) {
            return;
        }
        $media = Media::query()->findOrFail($mediaId);
        Gate::forUser($actor)->authorize('view', $media);
        abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 422);
    }

    private function validate(array $input, ?Official $official = null): array
    {
        return Validator::make($input, [
            'name' => ['required', 'string', 'max:255'], 'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('officials', 'slug')->ignore($official?->id)],
            'biography' => ['nullable', 'string', 'max:10000'],
            'photo_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'is_department_head' => ['required', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
