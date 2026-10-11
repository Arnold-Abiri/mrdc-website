<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static \UnitEnum|string|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 90;

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $modelLabel = 'site setting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')->disabled()->dehydrated(false),
            TextInput::make('key')->disabled()->dehydrated(false),
            TextInput::make('value')
                ->label('Value')
                ->maxLength(2000)
                ->visible(fn (?SiteSetting $record): bool => $record === null || $record->type !== 'textarea')
                ->helperText(fn (?SiteSetting $record): ?string => $record?->key === 'whatsapp_number' ? 'Digits only, e.g. 263771234567. Used for the WhatsApp Council Desk button.' : null),
            Textarea::make('value_textarea')
                ->label('Value')
                ->rows(3)
                ->maxLength(2000)
                ->visible(fn (?SiteSetting $record): bool => $record !== null && $record->type === 'textarea')
                ->dehydrated(false)
                ->formatStateUsing(fn (?SiteSetting $record): mixed => $record?->value),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')->badge()->sortable(),
                TextColumn::make('label')->searchable(),
                TextColumn::make('value')->limit(60)->searchable(),
            ])
            ->filters([
                SelectFilter::make('group')->options(['Contact' => 'Contact', 'Social' => 'Social']),
            ])
            ->recordActions([EditAction::make()])
            ->paginated(false);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteSettings::route('/'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
