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

    public function view(User $actor, Media $media): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'media.view', $media->department_id, $media->uploaded_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('media.create');
    }

    public function update(User $actor, Media $media): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'media.update', $media->department_id, $media->uploaded_by);
    }

    public function delete(User $actor, Media $media): bool
    {
        return false;
    }
}
