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

    public function view(User $actor, Tender $tender): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'tenders.view', $tender->department_id, $tender->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('tenders.create');
    }

    public function update(User $actor, Tender $tender): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'tenders.update', $tender->department_id, $tender->created_by);
    }

    public function publish(User $actor, Tender $tender): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'tenders.publish', $tender->department_id, $tender->created_by);
    }

    public function verify(User $actor, Tender $tender): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'tenders.verify', $tender->department_id, $tender->created_by);
    }

    public function award(User $actor, Tender $tender): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'tenders.award', $tender->department_id, $tender->created_by);
    }

    public function delete(User $actor, Tender $tender): bool
    {
        return false;
    }
}
