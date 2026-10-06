<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\CouncilProject;
use App\Models\User;

class CouncilProjectPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('projects.view');
    }

    public function view(User $actor, CouncilProject $project): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'projects.view', $project->department_id, $project->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('projects.create');
    }

    public function update(User $actor, CouncilProject $project): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'projects.update', $project->department_id, $project->created_by);
    }

    public function publish(User $actor, CouncilProject $project): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'projects.publish', $project->department_id, $project->created_by);
    }

    public function verify(User $actor, CouncilProject $project): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'projects.verify', $project->department_id, $project->created_by);
    }

    public function delete(User $actor, CouncilProject $project): bool
    {
        return false;
    }
}
