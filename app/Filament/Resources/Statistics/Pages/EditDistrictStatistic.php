<?php

namespace App\Filament\Resources\Statistics\Pages;

use App\Domain\Cms\DistrictStatisticManager;
use App\Filament\Resources\Statistics\DistrictStatisticResource;
use App\Models\DistrictStatistic;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDistrictStatistic extends EditRecord
{
    protected static string $resource = DistrictStatisticResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof DistrictStatistic, 404);

        return app(DistrictStatisticManager::class)->update(auth()->user(), $record, $data);
    }
}
