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

    public function view(User $actor, Vacancy $vacancy): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.view', $vacancy->department_id, $vacancy->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('vacancies.create');
    }

    public function update(User $actor, Vacancy $vacancy): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.update', $vacancy->department_id, $vacancy->created_by);
    }

    public function publish(User $actor, Vacancy $vacancy): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.publish', $vacancy->department_id, $vacancy->created_by);
    }

    public function verify(User $actor, Vacancy $vacancy): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'vacancies.verify', $vacancy->department_id, $vacancy->created_by);
    }

    public function delete(User $actor, Vacancy $vacancy): bool
    {
        return false;
    }
}
