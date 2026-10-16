<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\ProjectUpdate;
use App\Models\User;

class ProjectUpdatePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('projects.view');
    }

    public function view(User $actor, ProjectUpdate $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'projects.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'projects.create', null, $actor->id);
    }

    public function update(User $actor, ProjectUpdate $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'projects.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, ProjectUpdate $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'projects.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, ProjectUpdate $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'projects.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, ProjectUpdate $model): bool
    {
        return false;
    }
}
