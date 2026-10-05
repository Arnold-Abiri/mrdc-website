<?php

namespace App\Filament\Resources\PublicContacts\Pages;

use App\Domain\Cms\PublicContactManager;
use App\Filament\Resources\PublicContacts\PublicContactResource;
use App\Models\PublicContact;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPublicContact extends EditRecord
{
    protected static string $resource = PublicContactResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof PublicContact, 404);

        return app(PublicContactManager::class)->update(auth()->user(), $record, $data);
    }
}
