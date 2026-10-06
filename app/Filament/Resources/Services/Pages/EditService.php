<?php

namespace App\Filament\Resources\Services\Pages;

use App\Domain\Cms\ServiceManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditService extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = ServiceResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Service, 404);

        return app(ServiceManager::class)->update(auth()->user(), $record, $data);
    }
}
