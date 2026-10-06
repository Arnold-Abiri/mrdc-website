<?php

namespace App\Policies;

use App\Models\DistrictStatistic;
use App\Models\User;

class DistrictStatisticPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('statistics.view');
    }

    public function view(User $actor, DistrictStatistic $statistic): bool
    {
        return $actor->status === 'active' && $actor->can('statistics.view');
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('statistics.create');
    }

    public function update(User $actor, DistrictStatistic $statistic): bool
    {
        return $actor->status === 'active' && $actor->can('statistics.update');
    }

    public function delete(User $actor, DistrictStatistic $statistic): bool
    {
        return false;
    }
}
