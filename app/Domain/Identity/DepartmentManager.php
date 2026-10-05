<?php

namespace App\Domain\Identity;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DepartmentManager
{
    public function __construct(private AuditWriter $audit) {}

    public function create(User $actor, array $data): Department
    {
        Gate::forUser($actor)->authorize('create', Department::class);
        $values = Validator::make($data, ['name' => ['required', 'string', 'max:255', Rule::unique('departments')], 'code' => ['required', 'string', 'max:32', Rule::unique('departments')], 'description' => ['nullable', 'string'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'public_name' => ['nullable', 'string', 'max:255'], 'public_summary' => ['nullable', 'string', 'max:1000'], 'public_description' => ['nullable', 'string', 'max:20000'], 'responsibilities' => ['nullable', 'array', 'max:30'], 'responsibilities.*' => ['string', 'max:1000'], 'public_display_order' => ['nullable', 'integer', 'min:0', 'max:100000']])->validate();

        return DB::transaction(function () use ($actor, $values): Department {
            $department = Department::create($values);
            $this->audit->record($actor, 'department.created', $department);

            return $department;
        });
    }

    public function update(User $actor, Department $department, array $data): Department
    {
        Gate::forUser($actor)->authorize('update', $department);
        $values = Validator::make($data, ['name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)], 'code' => ['required', 'string', 'max:32', Rule::unique('departments')->ignore($department->id)], 'description' => ['nullable', 'string'], 'sort_order' => ['nullable', 'integer', 'min:0'], 'public_name' => ['nullable', 'string', 'max:255'], 'public_summary' => ['nullable', 'string', 'max:1000'], 'public_description' => ['nullable', 'string', 'max:20000'], 'responsibilities' => ['nullable', 'array', 'max:30'], 'responsibilities.*' => ['string', 'max:1000'], 'public_display_order' => ['nullable', 'integer', 'min:0', 'max:100000']])->validate();

        return DB::transaction(function () use ($actor, $department, $values): Department {
            $department->update($values);
            if (array_key_exists('public_name', $values) || array_key_exists('public_summary', $values) || array_key_exists('public_description', $values) || array_key_exists('responsibilities', $values)) {
                $department->public_status = 'draft';
                $department->public_verification_status = 'demo';
                $department->public_published_at = null;
                $department->save();
            }
            $this->audit->record($actor, 'department.updated', $department);

            return $department;
        });
    }

    public function setPublicVerification(User $actor, Department $department, string $state): void
    {
        Gate::forUser($actor)->authorize('verify', $department);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);
        DB::transaction(function () use ($actor, $department, $state): void {
            $department = Department::query()->lockForUpdate()->findOrFail($department->id);
            Gate::forUser($actor)->authorize('verify', $department);
            $department->public_verification_status = $state;
            if ($state !== 'publishable') {
                $department->public_status = 'unpublished';
                $department->public_published_at = null;
            }
            $department->save();
            $this->audit->record($actor, 'department.public_verification_changed', $department, ['state' => $state]);
        });
    }

    public function setPublicStatus(User $actor, Department $department, string $status): void
    {
        Gate::forUser($actor)->authorize('publish', $department);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);
        DB::transaction(function () use ($actor, $department, $status): void {
            $department = Department::query()->lockForUpdate()->findOrFail($department->id);
            Gate::forUser($actor)->authorize('publish', $department);
            abort_if($status === 'published' && $department->public_verification_status !== 'publishable', 422);
            abort_if($status === 'published' && trim((string) $department->public_name) === '', 422);
            $department->public_status = $status;
            $department->public_published_at = $status === 'published' ? now() : null;
            $department->save();
            $this->audit->record($actor, 'department.public_'.$status, $department);
        });
    }

    public function setActive(User $actor, Department $department, bool $active): void
    {
        Gate::forUser($actor)->authorize('disable', $department);
        DB::transaction(function () use ($actor, $department, $active): void {
            $department = Department::whereKey($department->id)->lockForUpdate()->firstOrFail();
            if (! $active && (User::where('department_id', $department->id)->where('status', 'active')->exists() || DB::table('user_role_scopes')->where('department_id', $department->id)->exists())) {
                throw ValidationException::withMessages(['status' => 'Move active users and role scopes before deactivating this department.']);
            }
            $department->status = $active ? 'active' : 'inactive';
            $department->save();
            $this->audit->record($actor, $active ? 'department.reactivated' : 'department.deactivated', $department);
        });
    }
}
