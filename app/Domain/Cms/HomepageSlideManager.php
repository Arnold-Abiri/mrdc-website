<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Models\HomepageSlide;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomepageSlideManager
{
    public function create(User $actor, array $input): HomepageSlide
    {
        Gate::forUser($actor)->authorize('create', HomepageSlide::class);
        $data = $this->validate($input);
        $this->authorizeMedia($actor, $data);

        return DB::transaction(function () use ($actor, $data): HomepageSlide {
            $slide = new HomepageSlide($data);
            $slide->created_by = $actor->id;
            $slide->updated_by = $actor->id;
            $slide->save();
            app(AuditWriter::class)->record($actor, 'slide.created', $slide, ['headline' => $slide->headline]);

            return $slide;
        });
    }

    public function update(User $actor, HomepageSlide $slide, array $input): HomepageSlide
    {
        Gate::forUser($actor)->authorize('update', $slide);
        $data = $this->validate($input);
        $this->authorizeMedia($actor, $data);

        return DB::transaction(function () use ($actor, $slide, $data): HomepageSlide {
            $slide->fill($data);
            $slide->status = 'draft';
            $slide->published_at = null;
            $slide->updated_by = $actor->id;
            $slide->save();
            app(AuditWriter::class)->record($actor, 'slide.updated', $slide, ['headline' => $slide->headline]);

            return $slide;
        });
    }

    public function setStatus(User $actor, HomepageSlide $slide, string $status): HomepageSlide
    {
        Gate::forUser($actor)->authorize('publish', $slide);
        abort_unless(in_array($status, ['published', 'unpublished'], true), 422);

        return DB::transaction(function () use ($actor, $slide, $status): HomepageSlide {
            $slide->status = $status;
            $slide->published_at = $status === 'published' ? now() : null;
            $slide->updated_by = $actor->id;
            $slide->save();
            app(AuditWriter::class)->record($actor, 'slide.'.$status, $slide, ['headline' => $slide->headline]);

            return $slide;
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
        $data = Validator::make($input, [
            'headline' => ['required', 'string', 'max:255'],
            'supporting_text' => ['nullable', 'string', 'max:1000'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],
        ])->validate();
        if (! empty($data['cta_url'])) {
            abort_unless(str_starts_with($data['cta_url'], '/'), 422);
        }

        return $data;
    }
}
