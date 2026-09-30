<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([Select::make('permission_names')->label('Permissions')->options(Permission::query()->pluck('name', 'name'))->multiple()]);
    }
}
