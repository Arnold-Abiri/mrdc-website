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

    public function view(User $actor, Gallery $gallery): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'galleries.view', $gallery->department_id, $gallery->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('galleries.create');
    }

    public function update(User $actor, Gallery $gallery): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'galleries.update', $gallery->department_id, $gallery->created_by);
    }

    public function publish(User $actor, Gallery $gallery): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'galleries.publish', $gallery->department_id, $gallery->created_by);
    }

    public function verify(User $actor, Gallery $gallery): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'galleries.verify', $gallery->department_id, $gallery->created_by);
    }

    public function delete(User $actor, Gallery $gallery): bool
    {
        return false;
    }
}
