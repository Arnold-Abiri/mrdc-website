<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Domain\Cms\CouncilProjectManager;
use App\Filament\Resources\Projects\CouncilProjectResource;
use App\Models\CouncilProject;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCouncilProject extends EditRecord
{
    protected static string $resource = CouncilProjectResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof CouncilProject, 404);

        return app(CouncilProjectManager::class)->update(auth()->user(), $record, $data);
    }
}
