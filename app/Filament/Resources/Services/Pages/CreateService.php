<?php

namespace App\Filament\Resources\Services\Pages;

use App\Domain\Cms\ServiceManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateService extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = ServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ServiceManager::class)->create(auth()->user(), $data);
    }
}
