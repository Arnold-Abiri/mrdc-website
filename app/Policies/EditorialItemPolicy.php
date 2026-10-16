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

    public function view(User $actor, EditorialItem $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'editorial.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'editorial.create', null, $actor->id);
    }

    public function update(User $actor, EditorialItem $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'editorial.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, EditorialItem $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'editorial.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, EditorialItem $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'editorial.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, EditorialItem $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'editorial.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
