<?php

namespace App\Domain\Identity;

use App\Enums\ScopeType;
use App\Models\User;
use App\Models\UserRoleScope;
use Illuminate\Database\Eloquent\Builder;

class DataScopeAuthorizer
{
    public function allows(User $actor, string $permission, ?int $departmentId, ?int $ownerId): bool
    {
        if ($actor->status !== 'active' || ! $actor->can($permission)) {
            return false;
        }
        $roleIds = $actor->roles()->whereHas('permissions', fn (Builder $query) => $query->where('name', $permission))->pluck('roles.id');
        $scopes = $actor->roleScopes()->whereIn('role_id', $roleIds)->get();
        foreach ($scopes as $scope) {
            if (! $scope instanceof UserRoleScope) {
                continue;
            }
            if ($scope->scope_type === ScopeType::Global->value) {
                return true;
            }
            if ($scope->scope_type === ScopeType::Department->value && $departmentId !== null && $scope->department_id === $departmentId) {
                return true;
            }
            if ($scope->scope_type === ScopeType::Own->value && $ownerId !== null && $ownerId === $actor->id) {
                return true;
            }
        }

        return false;
    }

    public function apply(Builder $query, User $actor, string $permission, string $departmentColumn = 'department_id', string $ownerColumn = 'user_id'): Builder
    {
        if ($actor->status !== 'active' || ! $actor->can($permission)) {
            return $query->whereRaw('1 = 0');
        }
        $roleIds = $actor->roles()->whereHas('permissions', fn (Builder $q) => $q->where('name', $permission))->pluck('roles.id');
        $scopes = $actor->roleScopes()->whereIn('role_id', $roleIds)->get();
        if ($scopes->contains('scope_type', ScopeType::Global->value)) {
            return $query;
        }
        $departments = $scopes->where('scope_type', ScopeType::Department->value)->pluck('department_id')->all();
        $own = $scopes->contains('scope_type', ScopeType::Own->value);

        return $query->where(function (Builder $q) use ($departments, $own, $actor, $departmentColumn, $ownerColumn): void {
            if ($departments !== []) {
                $q->whereIn($departmentColumn, $departments);
            }
            if ($own) {
                $q->orWhere($ownerColumn, $actor->id);
            }
            if ($departments === [] && ! $own) {
                $q->whereRaw('1 = 0');
            }
        });
    }
}
