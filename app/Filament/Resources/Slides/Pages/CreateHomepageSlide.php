<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Resources\Slides\HomepageSlideResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateHomepageSlide extends CreateRecord
{
    protected static string $resource = HomepageSlideResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(HomepageSlideManager::class)->create(auth()->user(), $data);
    }
}
