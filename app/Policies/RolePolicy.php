<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'roles.view', null, null);
    }

    public function view(User $actor, Role $role): bool
    {
        return $this->viewAny($actor);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'roles.create', null, null);
    }

    public function update(User $actor, Role $role): bool
    {
        return $actor->status === 'active' && $role->name !== 'System Administrator' && app(DataScopeAuthorizer::class)->allows($actor, 'roles.update', null, null);
    }

    public function assignPermissions(User $actor, Role $role): bool
    {
        return $actor->status === 'active' && $role->name !== 'System Administrator' && app(DataScopeAuthorizer::class)->allows($actor, 'roles.assign_permissions', null, null) && $actor->hasRole('System Administrator');
    }

    public function delete(User $actor, Role $role): bool
    {
        return false;
    }
}
