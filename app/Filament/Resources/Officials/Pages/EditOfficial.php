<?php

namespace App\Filament\Resources\Officials\Pages;

use App\Domain\Cms\OfficialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Officials\OfficialResource;
use App\Models\Official;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOfficial extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = OfficialResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Official, 404);

        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'photo_upload',
            targetKey: 'photo_media_id',
            altKey: 'photo_alt',
            fallbackTitle: $data['name'] ?? $record->name,
            titleSuffix: ' portrait',
        );

        return app(OfficialManager::class)->update(auth()->user(), $record, $data);
    }
}
