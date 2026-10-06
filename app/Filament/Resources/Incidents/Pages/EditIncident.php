<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Domain\Operations\IncidentManager;
use App\Filament\Resources\Incidents\IncidentResource;
use App\Models\Incident;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Incident, 404);

        return app(IncidentManager::class)->update(auth()->user(), $record, $data);
    }
}
