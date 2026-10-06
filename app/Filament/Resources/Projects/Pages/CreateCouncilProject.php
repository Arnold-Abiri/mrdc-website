<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Domain\Cms\CouncilProjectManager;
use App\Filament\Resources\Projects\CouncilProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCouncilProject extends CreateRecord
{
    protected static string $resource = CouncilProjectResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CouncilProjectManager::class)->create(auth()->user(), $data);
    }
}
