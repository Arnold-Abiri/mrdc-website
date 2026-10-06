<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Domain\Operations\IncidentManager;
use App\Filament\Resources\Incidents\IncidentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(IncidentManager::class)->create(auth()->user(), $data);
    }
}
