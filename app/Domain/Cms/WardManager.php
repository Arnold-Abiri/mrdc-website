<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WardManager
{
    public function create(User $actor, array $input): Ward
    {
        Gate::forUser($actor)->authorize('create', Ward::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'wards.create', null, $actor->id), 403);
        $ward = new Ward($data);
        $ward->created_by = $actor->id;
        $ward->updated_by = $actor->id;
        $ward->save();
        app(AuditWriter::class)->record($actor, 'wards.created', $ward, ['slug' => $ward->slug]);

        return $ward;
    }

    public function update(User $actor, Ward $ward, array $input): Ward
    {
        Gate::forUser($actor)->authorize('update', $ward);
        $data = $this->validate($input, $ward);

        return DB::transaction(function () use ($actor, $ward, $data): Ward {
            $ward = Ward::query()->lockForUpdate()->findOrFail($ward->id);
            Gate::forUser($actor)->authorize('update', $ward);
            $ward->fill($data);
            $ward->status = 'draft';
            $ward->verification_status = 'demo';
            $ward->published_at = null;
            $ward->updated_by = $actor->id;
            $ward->save();
            app(AuditWriter::class)->record($actor, 'wards.updated', $ward, ['slug' => $ward->slug]);

            return $ward;
        });
    }

    public function setVerification(User $actor, Ward $ward, string $state): Ward
    {
        Gate::forUser($actor)->authorize('verify', $ward);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $ward, $state): Ward {
            $ward = Ward::query()->lockForUpdate()->findOrFail($ward->id);
            Gate::forUser($actor)->authorize('verify', $ward);
            $ward->verification_status = $state;
            if ($state !== 'publishable') {
                $ward->status = 'unpublished';
                $ward->published_at = null;
            }
            $ward->updated_by = $actor->id;
            $ward->save();
            app(AuditWriter::class)->record($actor, 'wards.verification_changed', $ward, ['state' => $state]);

            return $ward;
        });
    }

    public function setStatus(User $actor, Ward $ward, string $status): Ward
    {
        Gate::forUser($actor)->authorize('publish', $ward);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $ward, $status): Ward {
            $ward = Ward::query()->lockForUpdate()->findOrFail($ward->id);
            Gate::forUser($actor)->authorize('publish', $ward);
            abort_if($status === 'published' && $ward->verification_status !== 'publishable', 422);
            $ward->status = $status;
            $ward->published_at = $status === 'published' ? now() : null;
            $ward->updated_by = $actor->id;
            $ward->save();
            app(AuditWriter::class)->record($actor, 'wards.'.$status, $ward, ['slug' => $ward->slug]);

            return $ward;
        });
    }

    private function validate(array $input, ?Ward $ward = null): array
    {
        return Validator::make($input, [
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('wards', 'slug')->ignore($ward?->id)],
            'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:5000'],
            'boundaries_description' => ['nullable', 'string', 'max:5000'], 'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
