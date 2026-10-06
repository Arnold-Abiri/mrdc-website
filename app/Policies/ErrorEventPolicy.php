<?php

namespace App\Policies;

use App\Models\ErrorEvent;
use App\Models\User;

class ErrorEventPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function view(User $actor, ErrorEvent $event): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function update(User $actor, ErrorEvent $event): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function delete(User $actor, ErrorEvent $event): bool
    {
        return false;
    }
}
