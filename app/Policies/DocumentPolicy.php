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

    public function view(User $actor, Document $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'documents.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'documents.create', null, $actor->id);
    }

    public function update(User $actor, Document $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'documents.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Document $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'documents.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Document $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'documents.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Document $model): bool
    {
        return false;
    }
}
