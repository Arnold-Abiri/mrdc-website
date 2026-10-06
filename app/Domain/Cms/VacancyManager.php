<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Document;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VacancyManager
{
    public function create(User $actor, array $input): Vacancy
    {
        Gate::forUser($actor)->authorize('create', Vacancy::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $data): Vacancy {
            $vacancy = new Vacancy($data);
            $vacancy->created_by = $actor->id;
            $vacancy->updated_by = $actor->id;
            $vacancy->save();
            app(AuditWriter::class)->record($actor, 'vacancy.created', $vacancy, ['slug' => $vacancy->slug]);

            return $vacancy;
        });
    }

    public function update(User $actor, Vacancy $vacancy, array $input): Vacancy
    {
        Gate::forUser($actor)->authorize('update', $vacancy);
        $data = $this->validate($input, $vacancy);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.update', $data['department_id'] ?? null, $vacancy->created_by), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $vacancy, $data): Vacancy {
            $vacancy = Vacancy::query()->lockForUpdate()->findOrFail($vacancy->id);
            Gate::forUser($actor)->authorize('update', $vacancy);
            $vacancy->fill($data);
            $vacancy->status = 'draft';
            $vacancy->verification_status = 'demo';
            $vacancy->published_at = null;
            $vacancy->verified_by = null;
            $vacancy->verified_at = null;
            $vacancy->updated_by = $actor->id;
            $vacancy->save();
            app(AuditWriter::class)->record($actor, 'vacancy.updated', $vacancy, ['slug' => $vacancy->slug]);

            return $vacancy;
        });
    }

    public function setVerification(User $actor, Vacancy $vacancy, string $state): Vacancy
    {
        Gate::forUser($actor)->authorize('verify', $vacancy);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $vacancy, $state): Vacancy {
            $vacancy = Vacancy::query()->lockForUpdate()->findOrFail($vacancy->id);
            Gate::forUser($actor)->authorize('verify', $vacancy);
            $vacancy->verification_status = $state;
            $vacancy->verified_by = $state === 'demo' ? null : $actor->id;
            $vacancy->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $vacancy->status = 'unpublished';
                $vacancy->published_at = null;
            }
            $vacancy->updated_by = $actor->id;
            $vacancy->save();
            app(AuditWriter::class)->record($actor, 'vacancy.verification_changed', $vacancy, ['state' => $state]);

            return $vacancy;
        });
    }

    public function setStatus(User $actor, Vacancy $vacancy, string $status): Vacancy
    {
        Gate::forUser($actor)->authorize('publish', $vacancy);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $vacancy, $status): Vacancy {
            $vacancy = Vacancy::query()->lockForUpdate()->findOrFail($vacancy->id);
            Gate::forUser($actor)->authorize('publish', $vacancy);
            abort_if($status === 'published' && $vacancy->verification_status !== 'publishable', 422);
            $vacancy->status = $status;
            $vacancy->published_at = $status === 'published' ? now() : null;
            $vacancy->updated_by = $actor->id;
            $vacancy->save();
            app(AuditWriter::class)->record($actor, 'vacancy.'.$status, $vacancy, ['slug' => $vacancy->slug]);

            return $vacancy;
        });
    }

    private function authorizeRelations(User $actor, array $data): void
    {
        if (! empty($data['document_id'])) {
            $document = Document::query()->findOrFail($data['document_id']);
            Gate::forUser($actor)->authorize('view', $document);
        }
    }

    private function validate(array $input, ?Vacancy $vacancy = null): array
    {
        return Validator::make($input, [
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('vacancies', 'slug')->ignore($vacancy?->id)],
            'title' => ['required', 'string', 'max:255'], 'grade' => ['nullable', 'string', 'max:60'],
            'reference' => ['nullable', 'string', 'max:60', Rule::unique('vacancies', 'reference')->ignore($vacancy?->id)],
            'employment_type' => ['nullable', Rule::in(['full_time', 'part_time', 'contract', 'temporary', 'internship'])],
            'description' => ['required', 'string', 'max:50000'],
            'responsibilities' => ['nullable', 'string', 'max:20000'], 'requirements' => ['nullable', 'string', 'max:20000'],
            'opens_at' => ['nullable', 'date'], 'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'application_instructions' => ['nullable', 'string', 'max:5000'],
            'document_id' => ['nullable', 'integer', Rule::exists('documents', 'id')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
