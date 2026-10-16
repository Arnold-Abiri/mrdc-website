<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Official;
use App\Models\User;

class OfficialPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('officials.view');
    }

    public function view(User $actor, Official $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'officials.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'officials.create', null, $actor->id);
    }

    public function update(User $actor, Official $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'officials.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Official $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'officials.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Official $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'officials.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Official $model): bool
    {
        return false;
    }
}
