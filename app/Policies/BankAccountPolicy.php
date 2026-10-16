<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\BankAccount;
use App\Models\User;

class BankAccountPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('bank_accounts.view');
    }

    public function view(User $actor, BankAccount $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'bank_accounts.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'bank_accounts.create', null, $actor->id);
    }

    public function update(User $actor, BankAccount $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'bank_accounts.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, BankAccount $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'bank_accounts.delete', null, $actor->id);
    }
}
