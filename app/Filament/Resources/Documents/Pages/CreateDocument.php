<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Domain\Cms\DocumentManager;
use App\Filament\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(DocumentManager::class)->create(auth()->user(), $data);
    }
}
