<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('galleries.view');
    }

    public function view(User $actor, Gallery $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'galleries.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'galleries.create', null, $actor->id);
    }

    public function update(User $actor, Gallery $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'galleries.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Gallery $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'galleries.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Gallery $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'galleries.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Gallery $model): bool
    {
        return false;
    }
}
