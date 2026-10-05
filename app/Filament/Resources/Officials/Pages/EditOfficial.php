<?php

namespace App\Filament\Resources\Officials\Pages;

use App\Domain\Cms\OfficialManager;
use App\Filament\Resources\Officials\OfficialResource;
use App\Models\Official;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOfficial extends EditRecord
{
    protected static string $resource = OfficialResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Official, 404);

        return app(OfficialManager::class)->update(auth()->user(), $record, $data);
    }
}
