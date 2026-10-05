<?php

namespace App\Filament\Resources\Wards\Pages;

use App\Domain\Cms\WardManager;
use App\Filament\Resources\Wards\WardResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateWard extends CreateRecord
{
    protected static string $resource = WardResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(WardManager::class)->create(auth()->user(), $data);
    }
}
