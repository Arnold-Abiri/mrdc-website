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

    public function view(User $actor, Page $page): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'pages.view', $page->department_id, $page->created_by);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('pages.create');
    }

    public function update(User $actor, Page $page): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'pages.update', $page->department_id, $page->created_by);
    }

    public function publish(User $actor, Page $page): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'pages.publish', $page->department_id, $page->created_by);
    }

    public function verify(User $actor, Page $page): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'pages.verify', $page->department_id, $page->created_by);
    }

    public function delete(User $actor, Page $page): bool
    {
        return false;
    }
}
