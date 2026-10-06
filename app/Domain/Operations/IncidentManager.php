<?php

namespace App\Domain\Operations;

use App\Domain\Identity\AuditWriter;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IncidentManager
{
    public function create(User $actor, array $input): Incident
    {
        Gate::forUser($actor)->authorize('create', Incident::class);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $data): Incident {
            $incident = new Incident($data);
            $incident->recorded_by = $actor->id;
            $incident->save();
            app(AuditWriter::class)->record($actor, 'incident.recorded', $incident, ['status' => $incident->status]);

            return $incident;
        });
    }

    public function update(User $actor, Incident $incident, array $input): Incident
    {
        Gate::forUser($actor)->authorize('update', $incident);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $incident, $data): Incident {
            $incident->fill($data);
            $incident->save();
            app(AuditWriter::class)->record($actor, 'incident.updated', $incident, ['status' => $incident->status]);

            return $incident;
        });
    }

    private function validate(array $input): array
    {
        return Validator::make($input, [
            'started_at' => ['required', 'date'],
            'recovered_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'source' => ['required', Rule::in(['manual', 'health_check', 'external_monitor'])],
            'status' => ['required', Rule::in(['open', 'monitoring', 'resolved'])],
            'summary' => ['nullable', 'string', 'max:500'],
        ])->validate();
    }
}
