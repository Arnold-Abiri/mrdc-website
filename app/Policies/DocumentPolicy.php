<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('documents.view');
    }

    public function view(User $actor, Document $document): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'documents.view', $document->department_id, $document->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('documents.create');
    }

    public function update(User $actor, Document $document): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'documents.update', $document->department_id, $document->created_by);
    }

    public function publish(User $actor, Document $document): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'documents.publish', $document->department_id, $document->created_by);
    }

    public function verify(User $actor, Document $document): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'documents.verify', $document->department_id, $document->created_by);
    }

    public function delete(User $actor, Document $document): bool
    {
        return false;
    }
}
