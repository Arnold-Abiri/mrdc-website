<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Slides\HomepageSlideResource;
use App\Models\HomepageSlide;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditHomepageSlide extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = HomepageSlideResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof HomepageSlide, 404);

        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'slide_image_upload',
            targetKey: 'media_id',
            altKey: 'slide_image_alt',
            fallbackTitle: $data['headline'] ?? $record->headline,
            titleSuffix: ' slide image',
        );

        return app(HomepageSlideManager::class)->update(auth()->user(), $record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
