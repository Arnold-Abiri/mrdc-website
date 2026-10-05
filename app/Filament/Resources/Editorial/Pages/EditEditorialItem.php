<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Domain\Cms\EditorialManager;
use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Models\EditorialItem;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEditorialItem extends EditRecord
{
    protected static string $resource = EditorialItemResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof EditorialItem, 404);

        return app(EditorialManager::class)->update(auth()->user(), $record, $data);
    }
}
