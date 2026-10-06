<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Domain\Cms\CouncilMeetingManager;
use App\Filament\Resources\Meetings\CouncilMeetingResource;
use App\Models\CouncilMeeting;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCouncilMeeting extends EditRecord
{
    protected static string $resource = CouncilMeetingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof CouncilMeeting, 404);

        return app(CouncilMeetingManager::class)->update(auth()->user(), $record, $data);
    }
}
