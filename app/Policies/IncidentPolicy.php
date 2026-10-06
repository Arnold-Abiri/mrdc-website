<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function view(User $actor, Incident $incident): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function update(User $actor, Incident $incident): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function delete(User $actor, Incident $incident): bool
    {
        return false;
    }
}
