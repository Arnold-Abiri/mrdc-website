<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active';
    }

    public function view(User $actor, Faq $faq): bool
    {
        return $actor->status === 'active';
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active';
    }

    public function update(User $actor, Faq $faq): bool
    {
        return $actor->status === 'active';
    }

    public function delete(User $actor, Faq $faq): bool
    {
        return $actor->status === 'active';
    }
}
