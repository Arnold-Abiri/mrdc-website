<?php

namespace App\Filament\Resources\PublicContacts\Pages;

use App\Domain\Cms\PublicContactManager;
use App\Filament\Resources\PublicContacts\PublicContactResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePublicContact extends CreateRecord
{
    protected static string $resource = PublicContactResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(PublicContactManager::class)->create(auth()->user(), $data);
    }
}
