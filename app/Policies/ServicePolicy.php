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

    public function view(User $actor, Service $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'services.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'services.create', null, $actor->id);
    }

    public function update(User $actor, Service $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'services.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Service $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'services.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Service $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'services.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Service $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'services.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
