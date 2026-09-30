<?php

namespace App\Domain\Identity;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleManager
{
    public function __construct(private AuditWriter $audit) {}

    public function create(User $actor, string $name): Role
    {
        Gate::forUser($actor)->authorize('create', Role::class);
        $values = Validator::make(['name' => $name], ['name' => ['required', 'string', 'max:255', Rule::unique('roles')]])->validate();

        return DB::transaction(function () use ($actor, $values): Role {
            $role = Role::query()->create(['name' => $values['name'], 'guard_name' => 'web']);
            $this->audit->record($actor, 'role.created', $role);

            return $role;
        });
    }

    public function syncPermissions(User $actor, Role $role, array $permissions): void
    {
        Gate::forUser($actor)->authorize('assignPermissions', $role);
        Validator::make(['permissions' => $permissions], ['permissions' => ['array'], 'permissions.*' => ['string', Rule::exists('permissions', 'name')]])->validate();
        DB::transaction(function () use ($actor, $role, $permissions): void {
            $role->syncPermissions($permissions);
            $this->audit->record($actor, 'role.permissions_changed', $role, ['permissions' => $permissions]);
        });
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
