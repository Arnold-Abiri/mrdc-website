<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GalleryManager
{
    public function create(User $actor, array $input): Gallery
    {
        Gate::forUser($actor)->authorize('create', Gallery::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'galleries.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeMedia($actor, $data);

        return DB::transaction(function () use ($actor, $data): Gallery {
            $images = $data['media'];
            unset($data['media']);
            $gallery = new Gallery($data);
            $gallery->created_by = $actor->id;
            $gallery->updated_by = $actor->id;
            $gallery->save();
            $this->syncImages($gallery, $images);
            app(AuditWriter::class)->record($actor, 'gallery.created', $gallery, ['slug' => $gallery->slug, 'images' => count($images)]);

            return $gallery;
        });
    }

    public function update(User $actor, Gallery $gallery, array $input): Gallery
    {
        Gate::forUser($actor)->authorize('update', $gallery);
        $data = $this->validate($input, $gallery);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'galleries.update', $data['department_id'] ?? null, $gallery->created_by), 403);
        $this->authorizeMedia($actor, $data);

        return DB::transaction(function () use ($actor, $gallery, $data): Gallery {
            $gallery = Gallery::query()->lockForUpdate()->findOrFail($gallery->id);
            Gate::forUser($actor)->authorize('update', $gallery);
            $images = $data['media'];
            unset($data['media']);
            $gallery->fill($data);
            $gallery->status = 'draft';
            $gallery->verification_status = 'demo';
            $gallery->published_at = null;
            $gallery->verified_by = null;
            $gallery->verified_at = null;
            $gallery->updated_by = $actor->id;
            $gallery->save();
            $this->syncImages($gallery, $images);
            app(AuditWriter::class)->record($actor, 'gallery.updated', $gallery, ['slug' => $gallery->slug]);

            return $gallery;
        });
    }

    public function setVerification(User $actor, Gallery $gallery, string $state): Gallery
    {
        Gate::forUser($actor)->authorize('verify', $gallery);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $gallery, $state): Gallery {
            $gallery = Gallery::query()->lockForUpdate()->findOrFail($gallery->id);
            Gate::forUser($actor)->authorize('verify', $gallery);
            $gallery->verification_status = $state;
            $gallery->verified_by = $state === 'demo' ? null : $actor->id;
            $gallery->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $gallery->status = 'unpublished';
                $gallery->published_at = null;
            }
            $gallery->updated_by = $actor->id;
            $gallery->save();
            app(AuditWriter::class)->record($actor, 'gallery.verification_changed', $gallery, ['state' => $state]);

            return $gallery;
        });
    }

    public function setStatus(User $actor, Gallery $gallery, string $status): Gallery
    {
        Gate::forUser($actor)->authorize('publish', $gallery);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $gallery, $status): Gallery {
            $gallery = Gallery::query()->lockForUpdate()->findOrFail($gallery->id);
            Gate::forUser($actor)->authorize('publish', $gallery);
            abort_if($status === 'published' && $gallery->verification_status !== 'publishable', 422);
            $gallery->status = $status;
            $gallery->published_at = $status === 'published' ? now() : null;
            $gallery->updated_by = $actor->id;
            $gallery->save();
            app(AuditWriter::class)->record($actor, 'gallery.'.$status, $gallery, ['slug' => $gallery->slug]);

            return $gallery;
        });
    }

    /**
     * Persist the ordered album images (media_id + caption) for a gallery.
     *
     * @param  list<array{media_id: int, caption?: string|null}>  $images
     */
    private function syncImages(Gallery $gallery, array $images): void
    {
        $sync = [];
        foreach ($images as $order => $image) {
            $sync[(int) $image['media_id']] = [
                'caption' => $image['caption'] ?? null,
                'display_order' => $order,
            ];
        }
        $gallery->media()->sync($sync);
    }

    private function authorizeMedia(User $actor, array $data): void
    {
        if (! empty($data['cover_media_id'])) {
            $this->authorizeImage($actor, (int) $data['cover_media_id']);
        }
        foreach ($data['media'] as $image) {
            $this->authorizeImage($actor, (int) $image['media_id']);
        }
    }

    private function authorizeImage(User $actor, int $mediaId): void
    {
        $media = Media::query()->findOrFail($mediaId);
        Gate::forUser($actor)->authorize('view', $media);
        abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 422);
    }

    private function validate(array $input, ?Gallery $gallery = null): array
    {
        return Validator::make($input, [
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('galleries', 'slug')->ignore($gallery?->id)],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'location' => ['nullable', 'string', 'max:255'],
            'album_date' => ['nullable', 'date'],
            'cover_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'is_featured' => ['required', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'media' => ['required', 'array', 'min:1', 'max:60'],
            'media.*.media_id' => ['required', 'integer', 'distinct', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'media.*.caption' => ['nullable', 'string', 'max:500'],
        ])->validate();
    }
}
