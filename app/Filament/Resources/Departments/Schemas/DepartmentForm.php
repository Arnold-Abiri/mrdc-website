<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(255)->unique(ignoreRecord: true), TextInput::make('code')->required()->maxLength(32)->unique(ignoreRecord: true), Textarea::make('description'), TextInput::make('sort_order')->numeric()->minValue(0)]);
    }
}
