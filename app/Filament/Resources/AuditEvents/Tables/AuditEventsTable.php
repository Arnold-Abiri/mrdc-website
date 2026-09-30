<?php

namespace App\Filament\Resources\AuditEvents\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([TextColumn::make('created_at')->dateTime()->sortable(), TextColumn::make('actor_id'), TextColumn::make('action')->searchable(), TextColumn::make('subject_type'), TextColumn::make('subject_id')]);
    }
}
