<?php

namespace App\Filament\Resources\AuditEvents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditEventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Audit Event Summary')
                ->description('Log of actions taken on system resources.')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('created_at')
                            ->label('Timestamp')
                            ->dateTime()
                            ->fontFamily('mono'),

                        TextEntry::make('actor_id')
                            ->label('Actor')
                            ->formatStateUsing(fn ($state): string => $state ? "User #{$state}" : 'System / Automated')
                            ->badge()
                            ->color(fn ($state): string => $state ? 'gray' : 'slate'),

                        TextEntry::make('action')
                            ->label('Operation')
                            ->badge()
                            ->color(fn (string $state): string => match (true) {
                                str_contains(strtolower($state), 'delete') || str_contains(strtolower($state), 'destroy') => 'danger',
                                str_contains(strtolower($state), 'create') || str_contains(strtolower($state), 'store') || str_contains(strtolower($state), 'publish') => 'success',
                                str_contains(strtolower($state), 'update') || str_contains(strtolower($state), 'edit') => 'info',
                                default => 'gray',
                            }),
                    ]),

                    Grid::make(2)->schema([
                        TextEntry::make('subject_type')
                            ->label('Target Entity Type')
                            ->fontFamily('mono'),

                        TextEntry::make('subject_id')
                            ->label('Entity Identifier')
                            ->fontFamily('mono')
                            ->placeholder('—'),
                    ]),
                ]),

            Section::make('Event Payload & Metadata')
                ->description('Event attributes and request parameters.')
                ->schema([
                    TextEntry::make('metadata')
                        ->label('')
                        ->formatStateUsing(fn ($state): string => is_array($state) || is_object($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : (string) ($state ?? 'No metadata recorded'))
                        ->fontFamily('mono')
                        ->size('xs')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
