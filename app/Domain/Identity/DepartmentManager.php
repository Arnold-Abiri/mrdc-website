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
        $values = Validator::make($data, ['name' => ['required', 'string', 'max:255', Rule::unique('departments')], 'code' => ['required', 'string', 'max:32', Rule::unique('departments')], 'description' => ['nullable', 'string'], 'sort_order' => ['nullable', 'integer', 'min:0']])->validate();

        return DB::transaction(function () use ($actor, $values): Department {
            $department = Department::create($values);
            $this->audit->record($actor, 'department.created', $department);

            return $department;
        });
    }

    public function update(User $actor, Department $department, array $data): Department
    {
        Gate::forUser($actor)->authorize('update', $department);
        $values = Validator::make($data, ['name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)], 'code' => ['required', 'string', 'max:32', Rule::unique('departments')->ignore($department->id)], 'description' => ['nullable', 'string'], 'sort_order' => ['nullable', 'integer', 'min:0']])->validate();

        return DB::transaction(function () use ($actor, $department, $values): Department {
            $department->update($values);
            $this->audit->record($actor, 'department.updated', $department);

            return $department;
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
