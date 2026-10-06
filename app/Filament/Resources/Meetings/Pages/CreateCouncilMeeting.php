<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Domain\Cms\CouncilMeetingManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Meetings\CouncilMeetingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCouncilMeeting extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = CouncilMeetingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CouncilMeetingManager::class)->create(auth()->user(), $data);
    }
}
