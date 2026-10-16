<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('media.view');
    }

    public function view(User $actor, Media $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'media.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'media.create', null, $actor->id);
    }

    public function update(User $actor, Media $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'media.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Media $model): bool
    {
        return false;
    }
}
