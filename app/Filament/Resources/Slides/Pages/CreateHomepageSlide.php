<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Slides\HomepageSlideResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateHomepageSlide extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = HomepageSlideResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'slide_image_upload',
            targetKey: 'media_id',
            altKey: 'slide_image_alt',
            fallbackTitle: $data['headline'] ?? null,
            titleSuffix: ' slide image',
        );

        return app(HomepageSlideManager::class)->create(auth()->user(), $data);
    }
}
