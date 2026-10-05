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

    public function view(User $actor, Official $official): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'officials.view', $official->department_id, $official->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('officials.create');
    }

    public function update(User $actor, Official $official): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'officials.update', $official->department_id, $official->created_by);
    }

    public function publish(User $actor, Official $official): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'officials.publish', $official->department_id, $official->created_by);
    }

    public function verify(User $actor, Official $official): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'officials.verify', $official->department_id, $official->created_by);
    }

    public function delete(User $actor, Official $official): bool
    {
        return false;
    }
}
