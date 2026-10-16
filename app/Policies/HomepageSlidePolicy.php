<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\HomepageSlide;
use App\Models\User;

class HomepageSlidePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('slides.view');
    }

    public function view(User $actor, HomepageSlide $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'slides.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'slides.create', null, $actor->id);
    }

    public function update(User $actor, HomepageSlide $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'slides.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, HomepageSlide $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'slides.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, HomepageSlide $model): bool
    {
        return false;
    }
}
