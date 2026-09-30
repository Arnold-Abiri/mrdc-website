<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\AuditEvent;
use App\Models\User;

class AuditEventPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'audit.view', null, null);
    }

    public function view(User $actor, AuditEvent $event): bool
    {
        return $this->viewAny($actor);
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function update(User $actor, AuditEvent $event): bool
    {
        return false;
    }

    public function delete(User $actor, AuditEvent $event): bool
    {
        return false;
    }
}
