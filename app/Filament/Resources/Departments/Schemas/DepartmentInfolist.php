<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DepartmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextEntry::make('name'), TextEntry::make('code'), TextEntry::make('description'), TextEntry::make('status'), TextEntry::make('sort_order')]);
    }
}
