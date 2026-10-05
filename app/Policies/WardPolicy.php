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

    public function view(User $actor, Ward $ward): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'wards.view', null, $ward->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('wards.create');
    }

    public function update(User $actor, Ward $ward): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'wards.update', null, $ward->created_by);
    }

    public function publish(User $actor, Ward $ward): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'wards.publish', null, $ward->created_by);
    }

    public function verify(User $actor, Ward $ward): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'wards.verify', null, $ward->created_by);
    }

    public function delete(User $actor, Ward $ward): bool
    {
        return false;
    }
}
