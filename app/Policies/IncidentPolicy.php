<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function view(User $actor, Incident $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'system-health.view', null, null);
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, Incident $model): bool
    {
        return false;
    }

    public function delete(User $actor, Incident $model): bool
    {
        return false;
    }
}
