<?php

namespace App\Filament\Resources\Tenders\Pages;

use App\Domain\Cms\TenderManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Tenders\TenderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTender extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = TenderResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(TenderManager::class)->create(auth()->user(), $data);
    }
}
