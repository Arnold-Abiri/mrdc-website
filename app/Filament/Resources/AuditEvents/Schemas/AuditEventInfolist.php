<?php

namespace App\Filament\Resources\AuditEvents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditEventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextEntry::make('created_at')->dateTime(), TextEntry::make('actor_id'), TextEntry::make('action'), TextEntry::make('subject_type'), TextEntry::make('subject_id'), TextEntry::make('metadata')->formatStateUsing(fn ($state) => json_encode($state))]);
    }
}
