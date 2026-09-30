<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextEntry::make('name'), TextEntry::make('email'), TextEntry::make('status'), TextEntry::make('department.name'), TextEntry::make('roles.name'),
            TextEntry::make('roleScopes.role.name')->label('Scoped roles'),
            TextEntry::make('roleScopes.scope_type')->label('Data scopes'),
            TextEntry::make('roleScopes.department.name')->label('Scope departments'), TextEntry::make('last_login_at')->dateTime()]);
    }
}
