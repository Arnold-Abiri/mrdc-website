<?php

namespace App\Filament\Resources\ProjectUpdates\Pages;

use App\Domain\Cms\ProjectUpdateManager;
use App\Filament\Resources\ProjectUpdates\ProjectUpdateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProjectUpdate extends CreateRecord
{
    protected static string $resource = ProjectUpdateResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ProjectUpdateManager::class)->create(auth()->user(), $data);
    }
}
