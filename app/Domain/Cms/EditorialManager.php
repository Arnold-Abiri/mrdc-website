<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EditorialManager
{
    public function create(User $actor, array $input): EditorialItem
    {
        Gate::forUser($actor)->authorize('create', EditorialItem::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'editorial.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $data): EditorialItem {
            $documents = $data['documents'] ?? [];
            unset($data['documents']);
            $item = new EditorialItem($data);
            $item->created_by = $actor->id;
            $item->updated_by = $actor->id;
            $item->save();
            $item->documents()->sync($documents);
            app(AuditWriter::class)->record($actor, 'editorial.created', $item, ['type' => $item->type, 'slug' => $item->slug]);

            return $item;
        });
    }

    public function update(User $actor, EditorialItem $item, array $input): EditorialItem
    {
        Gate::forUser($actor)->authorize('update', $item);
        $data = $this->validate($input, $item);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'editorial.update', $data['department_id'] ?? null, $item->created_by), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $item, $data): EditorialItem {
            $item = EditorialItem::query()->lockForUpdate()->findOrFail($item->id);
            Gate::forUser($actor)->authorize('update', $item);
            $documents = $data['documents'] ?? [];
            unset($data['documents']);
            $item->fill($data);
            $item->status = 'draft';
            $item->verification_status = 'demo';
            $item->published_at = null;
            $item->verified_by = null;
            $item->verified_at = null;
            $item->updated_by = $actor->id;
            $item->save();
            $item->documents()->sync($documents);
            app(AuditWriter::class)->record($actor, 'editorial.updated', $item, ['type' => $item->type, 'slug' => $item->slug]);

            return $item;
        });
    }

    public function setVerification(User $actor, EditorialItem $item, string $state): EditorialItem
    {
        Gate::forUser($actor)->authorize('verify', $item);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $item, $state): EditorialItem {
            $item = EditorialItem::query()->lockForUpdate()->findOrFail($item->id);
            Gate::forUser($actor)->authorize('verify', $item);
            $item->verification_status = $state;
            $item->verified_by = $state === 'demo' ? null : $actor->id;
            $item->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $item->status = 'unpublished';
                $item->published_at = null;
            }
            $item->updated_by = $actor->id;
            $item->save();
            app(AuditWriter::class)->record($actor, 'editorial.verification_changed', $item, ['state' => $state]);

            return $item;
        });
    }

    public function setStatus(User $actor, EditorialItem $item, string $status): EditorialItem
    {
        Gate::forUser($actor)->authorize('publish', $item);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $item, $status): EditorialItem {
            $item = EditorialItem::query()->lockForUpdate()->findOrFail($item->id);
            Gate::forUser($actor)->authorize('publish', $item);
            abort_if($status === 'published' && $item->verification_status !== 'publishable', 422);
            $item->status = $status;
            $item->published_at = $status === 'published' ? now() : null;
            $item->updated_by = $actor->id;
            $item->save();
            app(AuditWriter::class)->record($actor, 'editorial.'.$status, $item, ['type' => $item->type, 'slug' => $item->slug]);

            return $item;
        });
    }

    private function authorizeRelations(User $actor, array $data): void
    {
        if (! empty($data['featured_media_id'])) {
            $media = Media::query()->findOrFail($data['featured_media_id']);
            Gate::forUser($actor)->authorize('view', $media);
            abort_unless($media->status === 'active' && str_starts_with($media->mime_type, 'image/'), 422);
        }
        foreach ($data['documents'] ?? [] as $id) {
            $document = Document::query()->findOrFail($id);
            Gate::forUser($actor)->authorize('view', $document);
        }
    }

    private function validate(array $input, ?EditorialItem $item = null): array
    {
        return Validator::make($input, [
            'type' => ['required', Rule::in(['news', 'notice'])],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('editorial_items', 'slug')->ignore($item?->id)],
            'title' => ['required', 'string', 'max:255'], 'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:50000'], 'category' => ['nullable', 'string', 'max:60'],
            'featured_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('status', 'active')->where('mime_type', 'like', 'image/%'))],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'], 'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'documents' => ['nullable', 'array', 'max:20'], 'documents.*' => ['integer', 'distinct', Rule::exists('documents', 'id')],
        ])->validate();
    }
}
