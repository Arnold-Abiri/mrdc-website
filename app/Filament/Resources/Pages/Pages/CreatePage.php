<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Domain\Cms\PageManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePage extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = PageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(PageManager::class)->create(auth()->user(), $data);
    }
}
