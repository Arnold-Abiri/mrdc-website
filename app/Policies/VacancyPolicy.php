<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\User;
use App\Models\Vacancy;

class VacancyPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('vacancies.view');
    }

    public function view(User $actor, Vacancy $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.create', null, $actor->id);
    }

    public function update(User $actor, Vacancy $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Vacancy $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Vacancy $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Vacancy $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.delete', $model->department_id ?? null, $model->created_by ?? null);
    }
}
