<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Domain\Cms\GalleryManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Galleries\GalleryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGallery extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = GalleryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(GalleryManager::class)->create(auth()->user(), $data);
    }
}
