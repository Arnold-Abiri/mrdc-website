<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Domain\Cms\DocumentManager;
use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Document, 404);

        return app(DocumentManager::class)->update(auth()->user(), $record, $data);
    }
}
