<?php

namespace App\Policies;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Enquiry;
use App\Models\User;

class EnquiryPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->can('enquiries.view');
    }

    public function view(User $actor, Enquiry $enquiry): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'enquiries.view', $enquiry->department_id, null);
    }

    public function update(User $actor, Enquiry $enquiry): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'enquiries.update', $enquiry->department_id, null);
    }

    public function route(User $actor, Enquiry $enquiry): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'enquiries.route', $enquiry->department_id, null);
    }

    public function assign(User $actor, Enquiry $enquiry): bool
    {
        return app(DataScopeAuthorizer::class)->allows($actor, 'enquiries.assign', $enquiry->department_id, null);
    }

    public function create(User $actor): bool
    {
        return false;
    }

    public function delete(User $actor, Enquiry $enquiry): bool
    {
        return false;
    }
}
