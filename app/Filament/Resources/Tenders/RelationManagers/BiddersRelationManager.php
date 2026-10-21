<?php

namespace App\Filament\Resources\Tenders\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BiddersRelationManager extends RelationManager
{
    protected static string $relationship = 'bidders';

    protected static ?string $title = 'Registered Bidders & Specification Downloads';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('representative_name')
                    ->maxLength(160),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(254),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                DateTimePicker::make('downloaded_at')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('company_name')
            ->defaultSort('downloaded_at', 'desc')
            ->columns([
                TextColumn::make('company_name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('representative_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('downloaded_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Bidder Manually'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
