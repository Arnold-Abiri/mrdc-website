<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\InvestmentOpportunity;
use App\Models\User;

class InvestmentOpportunityPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('investment.view');
    }

    public function view(User $actor, InvestmentOpportunity $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'investment.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'investment.create', null, $actor->id);
    }

    public function update(User $actor, InvestmentOpportunity $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'investment.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, InvestmentOpportunity $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'investment.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, InvestmentOpportunity $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'investment.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, InvestmentOpportunity $model): bool
    {
        return false;
    }
}
