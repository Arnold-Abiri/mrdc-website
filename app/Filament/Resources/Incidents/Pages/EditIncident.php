<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Domain\Operations\IncidentManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Incidents\IncidentResource;
use App\Models\Incident;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    use HandlesTranslations;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Incident, 404);

        $updatedRecord = app(IncidentManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            DeleteAction::make(),
        ];
    }
}
