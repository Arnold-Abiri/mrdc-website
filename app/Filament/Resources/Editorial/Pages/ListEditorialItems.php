<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Filament\Resources\Editorial\EditorialItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEditorialItems extends ListRecords
{
    protected static string $resource = EditorialItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
