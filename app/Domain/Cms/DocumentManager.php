<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Document;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DocumentManager
{
    public function create(User $actor, array $input): Document
    {
        Gate::forUser($actor)->authorize('create', Document::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'documents.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeMedia($actor, $data['media_id']);

        return DB::transaction(function () use ($actor, $data): Document {
            $document = new Document($data);
            $document->created_by = $actor->id;
            $document->updated_by = $actor->id;
            $document->save();
            app(AuditWriter::class)->record($actor, 'documents.created', $document, ['slug' => $document->slug]);

            return $document;
        });
    }

    public function update(User $actor, Document $document, array $input): Document
    {
        Gate::forUser($actor)->authorize('update', $document);
        $data = $this->validate($input, $document);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'documents.update', $data['department_id'] ?? null, $document->created_by), 403);
        $this->authorizeMedia($actor, $data['media_id']);

        return DB::transaction(function () use ($actor, $document, $data): Document {
            $document = Document::query()->lockForUpdate()->findOrFail($document->id);
            Gate::forUser($actor)->authorize('update', $document);
            $document->fill($data);
            $document->status = 'draft';
            $document->published_at = null;
            $document->verification_status = 'demo';
            $document->verified_by = null;
            $document->verified_at = null;
            $document->updated_by = $actor->id;
            $document->save();
            app(AuditWriter::class)->record($actor, 'documents.updated', $document, ['slug' => $document->slug]);

            return $document;
        });
    }

    public function setVerification(User $actor, Document $document, string $state): Document
    {
        Gate::forUser($actor)->authorize('verify', $document);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $document, $state): Document {
            $document = Document::query()->lockForUpdate()->findOrFail($document->id);
            Gate::forUser($actor)->authorize('verify', $document);
            $document->verification_status = $state;
            $document->verified_by = $state === 'demo' ? null : $actor->id;
            $document->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $document->status = 'unpublished';
                $document->published_at = null;
            }
            $document->save();
            app(AuditWriter::class)->record($actor, 'documents.verification_changed', $document, ['state' => $state]);

            return $document;
        });
    }

    public function setStatus(User $actor, Document $document, string $status): Document
    {
        Gate::forUser($actor)->authorize('publish', $document);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $document, $status): Document {
            $document = Document::query()->lockForUpdate()->findOrFail($document->id);
            Gate::forUser($actor)->authorize('publish', $document);
            abort_if($status === 'published' && ($document->verification_status !== 'publishable' || ! $document->media instanceof Media || $document->media->status !== 'active'), 422);
            $document->status = $status;
            $document->published_at = $status === 'published' ? now() : null;
            $document->updated_by = $actor->id;
            $document->save();
            app(AuditWriter::class)->record($actor, 'documents.'.$status, $document, ['slug' => $document->slug]);

            return $document;
        });
    }

    private function authorizeMedia(User $actor, int $mediaId): void
    {
        $media = Media::query()->findOrFail($mediaId);
        Gate::forUser($actor)->authorize('view', $media);
        abort_unless($media->status === 'active', 422);
    }

    private function validate(array $input, ?Document $document = null): array
    {
        return Validator::make($input, [
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('documents', 'slug')->ignore($document?->id)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category' => ['required', Rule::in(['policy', 'report', 'plan', 'budget', 'form', 'notice', 'minutes', 'publication', 'other'])],
            'media_id' => ['required', 'integer', Rule::exists('media', 'id')->where('status', 'active')->where('mime_type', 'application/pdf')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'reference_date' => ['nullable', 'date'],
        ])->validate();
    }
}
