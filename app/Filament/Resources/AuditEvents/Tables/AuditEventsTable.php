<?php

namespace App\Filament\Resources\AuditEvents\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable()
                    ->fontFamily('mono')
                    ->size('xs'),

                TextColumn::make('actor_id')
                    ->label('Actor')
                    ->formatStateUsing(fn ($state): string => $state ? "User #{$state}" : 'System / CLI')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'gray' : 'slate'),

                TextColumn::make('action')
                    ->label('Action')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'delete') || str_contains(strtolower($state), 'destroy') => 'danger',
                        str_contains(strtolower($state), 'create') || str_contains(strtolower($state), 'store') || str_contains(strtolower($state), 'publish') => 'success',
                        str_contains(strtolower($state), 'update') || str_contains(strtolower($state), 'edit') => 'info',
                        str_contains(strtolower($state), 'login') || str_contains(strtolower($state), 'auth') => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Entity Type')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->fontFamily('mono')
                    ->size('xs'),

                TextColumn::make('subject_id')
                    ->label('Entity ID')
                    ->fontFamily('mono')
                    ->size('xs')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
