<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\PublicContact;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PublicContactManager
{
    public function create(User $actor, array $input): PublicContact
    {
        Gate::forUser($actor)->authorize('create', PublicContact::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'contacts.create', $data['department_id'] ?? null, $actor->id), 403);
        $contact = new PublicContact($data);
        $contact->is_public = false;
        $contact->created_by = $actor->id;
        $contact->updated_by = $actor->id;
        $contact->save();
        app(AuditWriter::class)->record($actor, 'contacts.created', $contact, ['type' => $contact->type]);

        return $contact;
    }

    public function update(User $actor, PublicContact $contact, array $input): PublicContact
    {
        Gate::forUser($actor)->authorize('update', $contact);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'contacts.update', $data['department_id'] ?? null, $contact->created_by), 403);

        return DB::transaction(function () use ($actor, $contact, $data): PublicContact {
            $contact = PublicContact::query()->lockForUpdate()->findOrFail($contact->id);
            Gate::forUser($actor)->authorize('update', $contact);
            $contact->fill($data);
            $contact->is_public = false;
            $contact->status = 'draft';
            $contact->verification_status = 'demo';
            $contact->published_at = null;
            $contact->updated_by = $actor->id;
            $contact->save();
            app(AuditWriter::class)->record($actor, 'contacts.updated', $contact, ['type' => $contact->type]);

            return $contact;
        });
    }

    public function setVerification(User $actor, PublicContact $contact, string $state): PublicContact
    {
        Gate::forUser($actor)->authorize('verify', $contact);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $contact, $state): PublicContact {
            $contact = PublicContact::query()->lockForUpdate()->findOrFail($contact->id);
            Gate::forUser($actor)->authorize('verify', $contact);
            $contact->verification_status = $state;
            if ($state !== 'publishable') {
                $contact->status = 'unpublished';
                $contact->published_at = null;
                $contact->is_public = false;
            }
            $contact->updated_by = $actor->id;
            $contact->save();
            app(AuditWriter::class)->record($actor, 'contacts.verification_changed', $contact, ['state' => $state]);

            return $contact;
        });
    }

    public function setStatus(User $actor, PublicContact $contact, string $status): PublicContact
    {
        Gate::forUser($actor)->authorize('publish', $contact);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $contact, $status): PublicContact {
            $contact = PublicContact::query()->lockForUpdate()->findOrFail($contact->id);
            Gate::forUser($actor)->authorize('publish', $contact);
            abort_if($status === 'published' && $contact->verification_status !== 'publishable', 422);
            $contact->status = $status;
            $contact->is_public = $status === 'published';
            $contact->published_at = $status === 'published' ? now() : null;
            $contact->updated_by = $actor->id;
            $contact->save();
            app(AuditWriter::class)->record($actor, 'contacts.'.$status, $contact, ['type' => $contact->type]);

            return $contact;
        });
    }

    private function validate(array $input): array
    {
        return Validator::make($input, [
            'office' => ['required', 'string', 'max:255'], 'type' => ['required', Rule::in(['phone', 'email', 'physical_address', 'postal_address'])],
            'value' => ['required', 'string', 'max:2000', Rule::when(($input['type'] ?? null) === 'email', ['email:rfc']), Rule::when(($input['type'] ?? null) === 'phone', ['regex:/^[+0-9().\s-]{5,40}$/'])], 'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
