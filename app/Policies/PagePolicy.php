<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('pages.view');
    }

    public function view(User $actor, Page $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'pages.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'pages.create', null, $actor->id);
    }

    public function update(User $actor, Page $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'pages.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function publish(User $actor, Page $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'pages.publish', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function verify(User $actor, Page $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'pages.verify', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, Page $model): bool
    {
        return false;
    }
}
