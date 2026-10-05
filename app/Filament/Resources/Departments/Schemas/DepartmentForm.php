<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(255)->unique(ignoreRecord: true), TextInput::make('code')->required()->maxLength(32)->unique(ignoreRecord: true), Textarea::make('description'), TextInput::make('sort_order')->numeric()->minValue(0), TextInput::make('public_name')->maxLength(255), Textarea::make('public_summary')->maxLength(1000), Textarea::make('public_description')->maxLength(20000), Repeater::make('responsibilities')->simple(Textarea::make('value'))->maxItems(30), TextInput::make('public_display_order')->numeric()->minValue(0)]);
    }
}
