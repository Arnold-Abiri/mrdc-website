<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('services.view');
    }

    public function view(User $actor, Service $service): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'services.view', $service->department_id, $service->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('services.create');
    }

    public function update(User $actor, Service $service): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'services.update', $service->department_id, $service->created_by);
    }

    public function publish(User $actor, Service $service): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'services.publish', $service->department_id, $service->created_by);
    }

    public function verify(User $actor, Service $service): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'services.verify', $service->department_id, $service->created_by);
    }

    public function delete(User $actor, Service $service): bool
    {
        return false;
    }
}
