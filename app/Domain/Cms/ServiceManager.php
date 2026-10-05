<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceManager
{
    public function create(User $actor, array $input): Service
    {
        Gate::forUser($actor)->authorize('create', Service::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'services.create', $data['department_id'] ?? null, $actor->id), 403);
        $service = new Service($data);
        $service->created_by = $actor->id;
        $service->updated_by = $actor->id;
        $service->save();
        app(AuditWriter::class)->record($actor, 'services.created', $service, ['slug' => $service->slug]);

        return $service;
    }

    public function update(User $actor, Service $service, array $input): Service
    {
        Gate::forUser($actor)->authorize('update', $service);
        $data = $this->validate($input, $service);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'services.update', $data['department_id'] ?? null, $service->created_by), 403);

        return DB::transaction(function () use ($actor, $service, $data): Service {
            $service = Service::query()->lockForUpdate()->findOrFail($service->id);
            Gate::forUser($actor)->authorize('update', $service);
            $service->fill($data);
            $service->status = 'draft';
            $service->verification_status = 'demo';
            $service->published_at = null;
            $service->updated_by = $actor->id;
            $service->save();
            app(AuditWriter::class)->record($actor, 'services.updated', $service, ['slug' => $service->slug]);

            return $service;
        });
    }

    public function setVerification(User $actor, Service $service, string $state): Service
    {
        Gate::forUser($actor)->authorize('verify', $service);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $service, $state): Service {
            $service = Service::query()->lockForUpdate()->findOrFail($service->id);
            Gate::forUser($actor)->authorize('verify', $service);
            $service->verification_status = $state;
            if ($state !== 'publishable') {
                $service->status = 'unpublished';
                $service->published_at = null;
            }
            $service->updated_by = $actor->id;
            $service->save();
            app(AuditWriter::class)->record($actor, 'services.verification_changed', $service, ['state' => $state]);

            return $service;
        });
    }

    public function setStatus(User $actor, Service $service, string $status): Service
    {
        Gate::forUser($actor)->authorize('publish', $service);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $service, $status): Service {
            $service = Service::query()->lockForUpdate()->findOrFail($service->id);
            Gate::forUser($actor)->authorize('publish', $service);
            abort_if($status === 'published' && $service->verification_status !== 'publishable', 422);
            $service->status = $status;
            $service->published_at = $status === 'published' ? now() : null;
            $service->updated_by = $actor->id;
            $service->save();
            app(AuditWriter::class)->record($actor, 'services.'.$status, $service, ['slug' => $service->slug]);

            return $service;
        });
    }

    private function validate(array $input, ?Service $service = null): array
    {
        return Validator::make($input, [
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('services', 'slug')->ignore($service?->id)],
            'name' => ['required', 'string', 'max:255'], 'summary' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:20000'], 'requirements' => ['nullable', 'array', 'max:30'], 'requirements.*' => ['string', 'max:1000'],
            'steps' => ['nullable', 'array', 'max:30'], 'steps.*' => ['string', 'max:1000'], 'fees_information' => ['nullable', 'string', 'max:5000'],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'], 'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ])->validate();
    }
}
