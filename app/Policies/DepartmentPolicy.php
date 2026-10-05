<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('departments.view');
    }

    public function view(User $actor, Department $department): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'departments.view', $department->id, null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'departments.create', null, null);
    }

    public function update(User $actor, Department $department): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'departments.update', $department->id, null);
    }

    public function disable(User $actor, Department $department): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'departments.disable', $department->id, null);
    }

    public function publish(User $actor, Department $department): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'departments.publish', $department->id, null);
    }

    public function verify(User $actor, Department $department): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'departments.verify', $department->id, null);
    }

    public function delete(User $actor, Department $department): bool
    {
        return false;
    }
}
