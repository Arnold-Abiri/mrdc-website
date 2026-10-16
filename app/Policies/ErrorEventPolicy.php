<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\ErrorEvent;
use App\Models\User;

class ErrorEventPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('system-health.view');
    }

    public function view(User $actor, ErrorEvent $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'system-health.view', null, null);
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, ErrorEvent $model): bool
    {
        return false;
    }

    public function delete(User $actor, ErrorEvent $model): bool
    {
        return false;
    }
}
