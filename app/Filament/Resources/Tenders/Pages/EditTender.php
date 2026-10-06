<?php

namespace App\Filament\Resources\Tenders\Pages;

use App\Domain\Cms\TenderManager;
use App\Filament\Resources\Tenders\TenderResource;
use App\Models\Tender;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTender extends EditRecord
{
    protected static string $resource = TenderResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Tender, 404);

        return app(TenderManager::class)->update(auth()->user(), $record, $data);
    }
}
