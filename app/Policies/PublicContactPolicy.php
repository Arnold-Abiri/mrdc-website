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

    public function view(User $actor, PublicContact $contact): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'contacts.view', $contact->department_id, $contact->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('contacts.create');
    }

    public function update(User $actor, PublicContact $contact): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'contacts.update', $contact->department_id, $contact->created_by);
    }

    public function publish(User $actor, PublicContact $contact): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'contacts.publish', $contact->department_id, $contact->created_by);
    }

    public function verify(User $actor, PublicContact $contact): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'contacts.verify', $contact->department_id, $contact->created_by);
    }

    public function delete(User $actor, PublicContact $contact): bool
    {
        return false;
    }
}
