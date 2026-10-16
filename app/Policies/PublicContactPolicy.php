<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\PublicContact;
use App\Models\User;

class PublicContactPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('contacts.view');
    }

    public function view(User $actor, PublicContact $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'contacts.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'contacts.create', null, $actor->id);
    }

    public function update(User $actor, PublicContact $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'contacts.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, PublicContact $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'contacts.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, PublicContact $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'contacts.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, PublicContact $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'contacts.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
