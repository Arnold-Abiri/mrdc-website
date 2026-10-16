<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\SiteSetting;
use App\Models\User;

class SiteSettingPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('site_settings.view');
    }

    public function view(User $actor, SiteSetting $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'site_settings.view', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function create(User $actor): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'site_settings.create', null, $actor->id);
    }

    public function update(User $actor, SiteSetting $model): bool
    {
        return $actor->status === 'active' && app(DataScopeAuthorizer::class)->allows($actor, 'site_settings.update', $model->department_id ?? null, $model->created_by ?? null);
    }

    public function delete(User $actor, SiteSetting $model): bool
    {
        return false;
    }
}
