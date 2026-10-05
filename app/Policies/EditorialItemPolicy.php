<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\EditorialItem;
use App\Models\User;

class EditorialItemPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('editorial.view');
    }

    public function view(User $actor, EditorialItem $item): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'editorial.view', $item->department_id, $item->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('editorial.create');
    }

    public function update(User $actor, EditorialItem $item): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'editorial.update', $item->department_id, $item->created_by);
    }

    public function publish(User $actor, EditorialItem $item): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'editorial.publish', $item->department_id, $item->created_by);
    }

    public function verify(User $actor, EditorialItem $item): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'editorial.verify', $item->department_id, $item->created_by);
    }

    public function delete(User $actor, EditorialItem $item): bool
    {
        return false;
    }
}
