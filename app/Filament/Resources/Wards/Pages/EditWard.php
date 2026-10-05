<?php

namespace App\Filament\Resources\Wards\Pages;

use App\Domain\Cms\WardManager;
use App\Filament\Resources\Wards\WardResource;
use App\Models\Ward;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditWard extends EditRecord
{
    protected static string $resource = WardResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Ward, 404);

        return app(WardManager::class)->update(auth()->user(), $record, $data);
    }
}
