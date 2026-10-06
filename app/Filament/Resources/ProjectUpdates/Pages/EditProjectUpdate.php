<?php

namespace App\Filament\Resources\ProjectUpdates\Pages;

use App\Domain\Cms\ProjectUpdateManager;
use App\Filament\Resources\ProjectUpdates\ProjectUpdateResource;
use App\Models\ProjectUpdate;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProjectUpdate extends EditRecord
{
    protected static string $resource = ProjectUpdateResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof ProjectUpdate, 404);

        return app(ProjectUpdateManager::class)->update(auth()->user(), $record, $data);
    }
}
