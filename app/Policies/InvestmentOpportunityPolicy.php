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

    public function view(User $actor, InvestmentOpportunity $opportunity): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'investment.view', $opportunity->department_id, $opportunity->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('investment.create');
    }

    public function update(User $actor, InvestmentOpportunity $opportunity): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'investment.update', $opportunity->department_id, $opportunity->created_by);
    }

    public function publish(User $actor, InvestmentOpportunity $opportunity): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'investment.publish', $opportunity->department_id, $opportunity->created_by);
    }

    public function verify(User $actor, InvestmentOpportunity $opportunity): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'investment.verify', $opportunity->department_id, $opportunity->created_by);
    }

    public function delete(User $actor, InvestmentOpportunity $opportunity): bool
    {
        return false;
    }
}
