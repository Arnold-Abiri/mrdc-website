<?php

namespace App\Filament\Resources\Officials\Pages;

use App\Domain\Cms\OfficialManager;
use App\Filament\Resources\Officials\OfficialResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOfficial extends CreateRecord
{
    protected static string $resource = OfficialResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(OfficialManager::class)->create(auth()->user(), $data);
    }
}
