<?php

namespace App\Filament\Resources\Officials\Pages;

use App\Domain\Cms\OfficialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Officials\OfficialResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOfficial extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = OfficialResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'photo_upload',
            targetKey: 'photo_media_id',
            altKey: 'photo_alt',
            fallbackTitle: $data['name'] ?? null,
            titleSuffix: ' portrait',
        );

        return app(OfficialManager::class)->create(auth()->user(), $data);
    }
}
