<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Tender;
use App\Models\User;

class TenderPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('tenders.view');
    }

    public function view(User $actor, Tender $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'tenders.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'tenders.create', null, $actor->id);
    }

    public function update(User $actor, Tender $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'tenders.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Tender $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'tenders.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Tender $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'tenders.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function award(User $actor, Tender $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'tenders.award', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Tender $model): bool
    {
        return false;
    }
}
