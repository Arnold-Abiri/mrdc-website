<?php

namespace App\Filament\Resources\PublicContacts\Pages;

use App\Filament\Resources\PublicContacts\PublicContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublicContacts extends ListRecords
{
    protected static string $resource = PublicContactResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
