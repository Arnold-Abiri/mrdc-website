<?php

namespace App\Policies;

use App\Models\ProjectUpdate;
use App\Models\User;

class ProjectUpdatePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('projects.view');
    }

    public function view(User $actor, ProjectUpdate $update): bool
    {
        return $actor->status === 'active' && $actor->can('projects.view');
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('projects.create');
    }

    public function update(User $actor, ProjectUpdate $update): bool
    {
        return $actor->status === 'active' && $actor->can('projects.update');
    }

    public function delete(User $actor, ProjectUpdate $update): bool
    {
        return false;
    }
}
