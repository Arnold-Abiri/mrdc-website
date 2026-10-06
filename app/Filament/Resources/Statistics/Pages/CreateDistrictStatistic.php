<?php

namespace App\Filament\Resources\Statistics\Pages;

use App\Domain\Cms\DistrictStatisticManager;
use App\Filament\Resources\Statistics\DistrictStatisticResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDistrictStatistic extends CreateRecord
{
    protected static string $resource = DistrictStatisticResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(DistrictStatisticManager::class)->create(auth()->user(), $data);
    }
}
