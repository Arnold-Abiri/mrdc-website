<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Document;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenderManager
{
    public function create(User $actor, array $input): Tender
    {
        Gate::forUser($actor)->authorize('create', Tender::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'tenders.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $data): Tender {
            $tender = new Tender($data);
            $tender->created_by = $actor->id;
            $tender->updated_by = $actor->id;
            $tender->save();
            app(AuditWriter::class)->record($actor, 'tender.created', $tender, ['reference' => $tender->reference, 'slug' => $tender->slug]);

            return $tender;
        });
    }

    public function update(User $actor, Tender $tender, array $input): Tender
    {
        Gate::forUser($actor)->authorize('update', $tender);
        $data = $this->validate($input, $tender);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'tenders.update', $data['department_id'] ?? null, $tender->created_by), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $tender, $data): Tender {
            $tender = Tender::query()->lockForUpdate()->findOrFail($tender->id);
            Gate::forUser($actor)->authorize('update', $tender);
            $tender->fill($data);
            $tender->status = 'draft';
            $tender->verification_status = 'demo';
            $tender->published_at = null;
            $tender->verified_by = null;
            $tender->verified_at = null;
            $tender->updated_by = $actor->id;
            $tender->save();
            app(AuditWriter::class)->record($actor, 'tender.updated', $tender, ['reference' => $tender->reference, 'slug' => $tender->slug]);

            return $tender;
        });
    }

    public function setVerification(User $actor, Tender $tender, string $state): Tender
    {
        Gate::forUser($actor)->authorize('verify', $tender);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $tender, $state): Tender {
            $tender = Tender::query()->lockForUpdate()->findOrFail($tender->id);
            Gate::forUser($actor)->authorize('verify', $tender);
            $tender->verification_status = $state;
            $tender->verified_by = $state === 'demo' ? null : $actor->id;
            $tender->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $tender->status = 'unpublished';
                $tender->published_at = null;
            }
            $tender->updated_by = $actor->id;
            $tender->save();
            app(AuditWriter::class)->record($actor, 'tender.verification_changed', $tender, ['state' => $state]);

            return $tender;
        });
    }

    public function setStatus(User $actor, Tender $tender, string $status): Tender
    {
        Gate::forUser($actor)->authorize('publish', $tender);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $tender, $status): Tender {
            $tender = Tender::query()->lockForUpdate()->findOrFail($tender->id);
            Gate::forUser($actor)->authorize('publish', $tender);
            abort_if($status === 'published' && $tender->verification_status !== 'publishable', 422);
            $tender->status = $status;
            $tender->published_at = $status === 'published' ? now() : null;
            $tender->updated_by = $actor->id;
            $tender->save();
            app(AuditWriter::class)->record($actor, 'tender.'.$status, $tender, ['reference' => $tender->reference, 'slug' => $tender->slug]);

            return $tender;
        });
    }

    public function setLifecycleStatus(User $actor, Tender $tender, string $lifecycle): Tender
    {
        Gate::forUser($actor)->authorize('update', $tender);
        abort_unless(in_array($lifecycle, ['upcoming', 'open', 'closed', 'awarded', 'cancelled'], true), 422);

        return DB::transaction(function () use ($actor, $tender, $lifecycle): Tender {
            $tender = Tender::query()->lockForUpdate()->findOrFail($tender->id);
            Gate::forUser($actor)->authorize('update', $tender);
            $tender->lifecycle_status = $lifecycle;
            $tender->updated_by = $actor->id;
            $tender->save();
            app(AuditWriter::class)->record($actor, 'tender.lifecycle_changed', $tender, ['lifecycle' => $lifecycle]);

            return $tender;
        });
    }

    private function authorizeRelations(User $actor, array $data): void
    {
        if (! empty($data['document_id'])) {
            $document = Document::query()->findOrFail($data['document_id']);
            Gate::forUser($actor)->authorize('view', $document);
        }
    }

    private function validate(array $input, ?Tender $tender = null): array
    {
        return Validator::make($input, [
            'reference' => ['required', 'string', 'max:60', Rule::unique('tenders', 'reference')->ignore($tender?->id)],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('tenders', 'slug')->ignore($tender?->id)],
            'title' => ['required', 'string', 'max:255'], 'category' => ['nullable', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:50000'],
            'opens_at' => ['nullable', 'date'], 'closes_at' => ['nullable', 'date'],
            'lifecycle_status' => ['required', Rule::in(['upcoming', 'open', 'closed', 'awarded', 'cancelled'])],
            'contact_instructions' => ['nullable', 'string', 'max:5000'],
            'document_id' => ['nullable', 'integer', Rule::exists('documents', 'id')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
