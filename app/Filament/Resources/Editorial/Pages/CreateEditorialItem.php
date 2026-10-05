<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Domain\Cms\EditorialManager;
use App\Filament\Resources\Editorial\EditorialItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEditorialItem extends CreateRecord
{
    protected static string $resource = EditorialItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(EditorialManager::class)->create(auth()->user(), $data);
    }
}
