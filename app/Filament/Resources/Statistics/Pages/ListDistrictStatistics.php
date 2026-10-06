<?php

namespace App\Filament\Resources\Statistics\Pages;

use App\Filament\Resources\Statistics\DistrictStatisticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDistrictStatistics extends ListRecords
{
    protected static string $resource = DistrictStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
