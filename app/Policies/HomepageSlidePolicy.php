<?php

namespace App\Policies;

use App\Models\HomepageSlide;
use App\Models\User;

class HomepageSlidePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('slides.view');
    }

    public function view(User $actor, HomepageSlide $slide): bool
    {
        return $actor->status === 'active' && $actor->can('slides.view');
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('slides.create');
    }

    public function update(User $actor, HomepageSlide $slide): bool
    {
        return $actor->status === 'active' && $actor->can('slides.update');
    }

    public function publish(User $actor, HomepageSlide $slide): bool
    {
        return $actor->status === 'active' && $actor->can('slides.publish');
    }

    public function delete(User $actor, HomepageSlide $slide): bool
    {
        return false;
    }
}
