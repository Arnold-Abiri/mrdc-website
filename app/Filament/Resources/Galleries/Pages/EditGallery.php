<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Domain\Cms\GalleryManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\Gallery;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditGallery extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = GalleryResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Gallery, 404);

        return app(GalleryManager::class)->update(auth()->user(), $record, $data);
    }
}
