<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\CouncilProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCouncilProjects extends ListRecords
{
    protected static string $resource = CouncilProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
