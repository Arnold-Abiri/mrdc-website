<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\CouncilMeetingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCouncilMeetings extends ListRecords
{
    protected static string $resource = CouncilMeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
