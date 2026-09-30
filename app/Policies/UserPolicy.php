<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('users.view');
    }

    public function view(User $actor, User $subject): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'users.view', $subject->department_id, $subject->id);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'users.create', null, null);
    }

    public function update(User $actor, User $subject): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'users.update', $subject->department_id, $subject->id);
    }

    public function disable(User $actor, User $subject): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'users.disable', $subject->department_id, $subject->id) && $actor->id !== $subject->id;
    }

    public function assignRoles(User $actor, User $subject): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'users.assign_roles', null, null) && $actor->hasRole('System Administrator');
    }

    public function delete(User $actor, User $subject): bool
    {
        return false;
    }
}
