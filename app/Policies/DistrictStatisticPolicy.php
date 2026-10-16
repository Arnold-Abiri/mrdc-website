<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\DistrictStatistic;
use App\Models\User;

class DistrictStatisticPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('statistics.view');
    }

    public function view(User $actor, DistrictStatistic $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'statistics.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'statistics.create', null, $actor->id);
    }

    public function update(User $actor, DistrictStatistic $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'statistics.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, DistrictStatistic $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'statistics.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, DistrictStatistic $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'statistics.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, DistrictStatistic $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'statistics.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
