<?php

namespace App\Domain\Identity;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserManager
{
    public function __construct(private AuditWriter $audit) {}

    public function create(User $actor, array $data): User
    {
        Gate::forUser($actor)->authorize('create', User::class);
        if (isset($data['email']) && is_string($data['email'])) {
            $data['email'] = Str::lower(trim($data['email']));
        }
        $values = Validator::make($data, ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')], 'department_id' => ['nullable', Rule::exists('departments', 'id')->where('status', 'active')]])->validate();
        if (config('mail.default') === 'log') {
            throw ValidationException::withMessages(['email' => 'Configure a secure mail transport before creating users.']);
        }

        return DB::transaction(function () use ($actor, $values): User {
            if (isset($values['department_id'])) {
                Department::whereKey($values['department_id'])->where('status', 'active')->lockForUpdate()->firstOrFail();
            }
            $user = User::create(['name' => $values['name'], 'email' => Str::lower(trim($values['email'])), 'password' => Hash::make(Str::random(64))]);
            $user->department_id = $values['department_id'] ?? null;
            $user->status = 'active';
            $user->save();
            Password::sendResetLink(['email' => $user->email]);
            $this->audit->record($actor, 'user.created', $user);

            return $user;
        });
    }

    public function update(User $actor, User $user, array $data): User
    {
        Gate::forUser($actor)->authorize('update', $user);
        if (isset($data['email']) && is_string($data['email'])) {
            $data['email'] = Str::lower(trim($data['email']));
        }
        $values = Validator::make($data, ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)], 'department_id' => ['nullable', Rule::exists('departments', 'id')->where('status', 'active')]])->validate();

        return DB::transaction(function () use ($actor, $user, $values): User {
            if (isset($values['department_id'])) {
                Department::whereKey($values['department_id'])->where('status', 'active')->lockForUpdate()->firstOrFail();
            }
            $user->name = $values['name'];
            $user->email = Str::lower(trim($values['email']));
            $user->department_id = $values['department_id'] ?? null;
            $user->save();
            $this->audit->record($actor, 'user.updated', $user);

            return $user;
        });
    }

    public function setActive(User $actor, User $user, bool $active): void
    {
        Gate::forUser($actor)->authorize('disable', $user);
        if (! $active && $actor->id === $user->id) {
            throw ValidationException::withMessages(['status' => 'You cannot disable your own account.']);
        }
        DB::transaction(function () use ($actor, $user, $active): void {
            Role::where('name', 'System Administrator')->lockForUpdate()->firstOrFail();
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if (! $active && $user->hasRole('System Administrator') && User::where('status', 'active')->whereHas('roles', fn ($q) => $q->where('name', 'System Administrator'))->count() <= 1) {
                throw ValidationException::withMessages(['status' => 'The final System Administrator cannot be disabled.']);
            }
            $user->status = $active ? 'active' : 'disabled';
            $user->disabled_at = $active ? null : now();
            $user->disabled_by = $active ? null : $actor->id;
            $user->save();
            $this->audit->record($actor, $active ? 'user.reactivated' : 'user.disabled', $user);
        });
    }

    public function assignRole(User $actor, User $user, Role $role, string $scopeType, ?int $departmentId = null): void
    {
        Gate::forUser($actor)->authorize('assignRoles', $user);
        Validator::make(['scope_type' => $scopeType, 'department_id' => $departmentId], ['scope_type' => ['required', Rule::in(['global', 'department', 'own'])], 'department_id' => [$scopeType === 'department' ? 'required' : 'nullable', $scopeType === 'department' ? 'present' : 'prohibited', Rule::exists('departments', 'id')->where('status', 'active')]])->validate();
        if ($role->name === 'System Administrator' && $scopeType !== 'global') {
            throw ValidationException::withMessages(['scope_type' => 'System Administrator requires global scope.']);
        }
        DB::transaction(function () use ($actor, $user, $role, $scopeType, $departmentId): void {
            Role::where('name', 'System Administrator')->lockForUpdate()->firstOrFail();
            if ($departmentId !== null) {
                Department::whereKey($departmentId)->where('status', 'active')->lockForUpdate()->firstOrFail();
            }
            $user->assignRole($role);
            DB::table('user_role_scopes')->updateOrInsert(['user_id' => $user->id, 'role_id' => $role->id], ['scope_type' => $scopeType, 'department_id' => $departmentId, 'created_at' => now(), 'updated_at' => now()]);
            $this->audit->record($actor, 'user.role_assigned', $user, ['role_id' => $role->id, 'scope_type' => $scopeType, 'department_id' => $departmentId]);
        });
    }

    public function removeRole(User $actor, User $user, Role $role): void
    {
        Gate::forUser($actor)->authorize('assignRoles', $user);
        if ($actor->id === $user->id && $role->name === 'System Administrator') {
            throw ValidationException::withMessages(['role' => 'You cannot remove your own System Administrator role.']);
        }
        DB::transaction(function () use ($actor, $user, $role): void {
            Role::where('name', 'System Administrator')->lockForUpdate()->firstOrFail();
            if ($role->name === 'System Administrator' && User::where('status', 'active')->whereHas('roles', fn ($q) => $q->where('name', 'System Administrator'))->count() <= 1) {
                throw ValidationException::withMessages(['role' => 'The final System Administrator role cannot be removed.']);
            }
            $user->removeRole($role);
            DB::table('user_role_scopes')->where('user_id', $user->id)->where('role_id', $role->id)->delete();
            $this->audit->record($actor, 'user.role_removed', $user, ['role_id' => $role->id]);
        });
    }
}
