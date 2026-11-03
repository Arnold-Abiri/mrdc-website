<?php

namespace App\Filament\Resources\Statistics\Pages;

use App\Domain\Cms\DistrictStatisticManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Statistics\DistrictStatisticResource;
use App\Models\DistrictStatistic;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDistrictStatistic extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = DistrictStatisticResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof DistrictStatistic, 404);

        $updatedRecord = app(DistrictStatisticManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
