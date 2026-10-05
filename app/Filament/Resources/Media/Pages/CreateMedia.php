<?php

namespace App\Filament\Resources\Media\Pages;

use App\Domain\Cms\MediaManager;
use App\Filament\Resources\Media\MediaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $file = $data['file'];
        unset($data['file']);

        return app(MediaManager::class)->upload(auth()->user(), $file, $data);
    }
}
