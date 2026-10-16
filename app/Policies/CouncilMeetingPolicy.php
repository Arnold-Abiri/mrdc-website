<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\CouncilMeeting;
use App\Models\User;

class CouncilMeetingPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('meetings.view');
    }

    public function view(User $actor, CouncilMeeting $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'meetings.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'meetings.create', null, $actor->id);
    }

    public function update(User $actor, CouncilMeeting $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'meetings.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, CouncilMeeting $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'meetings.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, CouncilMeeting $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'meetings.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, CouncilMeeting $model): bool
    {
        return false;
    }
}
