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

    public function view(User $actor, CouncilMeeting $meeting): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'meetings.view', $meeting->department_id, $meeting->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('meetings.create');
    }

    public function update(User $actor, CouncilMeeting $meeting): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'meetings.update', $meeting->department_id, $meeting->created_by);
    }

    public function publish(User $actor, CouncilMeeting $meeting): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'meetings.publish', $meeting->department_id, $meeting->created_by);
    }

    public function verify(User $actor, CouncilMeeting $meeting): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'meetings.verify', $meeting->department_id, $meeting->created_by);
    }

    public function delete(User $actor, CouncilMeeting $meeting): bool
    {
        return false;
    }
}
