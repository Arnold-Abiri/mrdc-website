<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\User;
use App\Models\Ward;

class WardPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('wards.view');
    }

    public function view(User $actor, Ward $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'wards.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'wards.create', null, $actor->id);
    }

    public function update(User $actor, Ward $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'wards.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Ward $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'wards.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Ward $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'wards.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Ward $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'wards.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
