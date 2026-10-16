<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('faqs.view');
    }

    public function view(User $actor, Faq $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'faqs.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'faqs.create', null, $actor->id);
    }

    public function update(User $actor, Faq $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'faqs.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Faq $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'faqs.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
