<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Document;
use App\Models\InvestmentOpportunity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvestmentManager
{
    public function create(User $actor, array $input): InvestmentOpportunity
    {
        Gate::forUser($actor)->authorize('create', InvestmentOpportunity::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'investment.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $data): InvestmentOpportunity {
            $opportunity = new InvestmentOpportunity($data);
            $opportunity->created_by = $actor->id;
            $opportunity->updated_by = $actor->id;
            $opportunity->save();
            app(AuditWriter::class)->record($actor, 'investment.created', $opportunity, ['slug' => $opportunity->slug]);

            return $opportunity;
        });
    }

    public function update(User $actor, InvestmentOpportunity $opportunity, array $input): InvestmentOpportunity
    {
        Gate::forUser($actor)->authorize('update', $opportunity);
        $data = $this->validate($input, $opportunity);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'investment.update', $data['department_id'] ?? null, $opportunity->created_by), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $opportunity, $data): InvestmentOpportunity {
            $opportunity = InvestmentOpportunity::query()->lockForUpdate()->findOrFail($opportunity->id);
            Gate::forUser($actor)->authorize('update', $opportunity);
            $opportunity->fill($data);
            $opportunity->status = 'draft';
            $opportunity->verification_status = 'demo';
            $opportunity->published_at = null;
            $opportunity->verified_by = null;
            $opportunity->verified_at = null;
            $opportunity->updated_by = $actor->id;
            $opportunity->save();
            app(AuditWriter::class)->record($actor, 'investment.updated', $opportunity, ['slug' => $opportunity->slug]);

            return $opportunity;
        });
    }

    public function setVerification(User $actor, InvestmentOpportunity $opportunity, string $state): InvestmentOpportunity
    {
        Gate::forUser($actor)->authorize('verify', $opportunity);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $opportunity, $state): InvestmentOpportunity {
            $opportunity = InvestmentOpportunity::query()->lockForUpdate()->findOrFail($opportunity->id);
            Gate::forUser($actor)->authorize('verify', $opportunity);
            $opportunity->verification_status = $state;
            $opportunity->verified_by = $state === 'demo' ? null : $actor->id;
            $opportunity->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $opportunity->status = 'unpublished';
                $opportunity->published_at = null;
            }
            $opportunity->updated_by = $actor->id;
            $opportunity->save();
            app(AuditWriter::class)->record($actor, 'investment.verification_changed', $opportunity, ['state' => $state]);

            return $opportunity;
        });
    }

    public function setStatus(User $actor, InvestmentOpportunity $opportunity, string $status): InvestmentOpportunity
    {
        Gate::forUser($actor)->authorize('publish', $opportunity);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $opportunity, $status): InvestmentOpportunity {
            $opportunity = InvestmentOpportunity::query()->lockForUpdate()->findOrFail($opportunity->id);
            Gate::forUser($actor)->authorize('publish', $opportunity);
            abort_if($status === 'published' && $opportunity->verification_status !== 'publishable', 422);
            $opportunity->status = $status;
            $opportunity->published_at = $status === 'published' ? now() : null;
            $opportunity->updated_by = $actor->id;
            $opportunity->save();
            app(AuditWriter::class)->record($actor, 'investment.'.$status, $opportunity, ['slug' => $opportunity->slug]);

            return $opportunity;
        });
    }

    private function authorizeRelations(User $actor, array $data): void
    {
        if (! empty($data['document_id'])) {
            $document = Document::query()->findOrFail($data['document_id']);
            Gate::forUser($actor)->authorize('view', $document);
        }
    }

    private function validate(array $input, ?InvestmentOpportunity $opportunity = null): array
    {
        return Validator::make($input, [
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('investment_opportunities', 'slug')->ignore($opportunity?->id)],
            'title' => ['required', 'string', 'max:255'], 'sector' => ['nullable', 'string', 'max:60'],
            'summary' => ['nullable', 'string', 'max:1000'], 'description' => ['required', 'string', 'max:50000'],
            'location' => ['nullable', 'string', 'max:255'],
            'opportunity_status' => ['required', Rule::in(['open', 'prospecting', 'committed', 'closed'])],
            'document_id' => ['nullable', 'integer', Rule::exists('documents', 'id')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
