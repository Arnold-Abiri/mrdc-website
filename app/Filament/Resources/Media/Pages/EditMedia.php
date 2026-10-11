<?php

namespace App\Filament\Resources\Media\Pages;

use App\Domain\Cms\MediaManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    use HandlesTranslations;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Media, 404);

        return app(MediaManager::class)->update(auth()->user(), $record, $data);
    }
}
