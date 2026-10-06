<?php

namespace App\Filament\Resources\Statistics;

use App\Domain\Cms\DistrictStatisticManager;
use App\Filament\Resources\Statistics\Pages\CreateDistrictStatistic;
use App\Filament\Resources\Statistics\Pages\EditDistrictStatistic;
use App\Filament\Resources\Statistics\Pages\ListDistrictStatistics;
use App\Models\DistrictStatistic;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DistrictStatisticResource extends Resource
{
    protected static ?string $model = DistrictStatistic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'District statistics';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')->required()->maxLength(120)->helperText('e.g. Electoral wards, District population.'),
            TextInput::make('value')->required()->maxLength(60)->helperText('e.g. 29, 161091.'),
            TextInput::make('unit')->maxLength(60)->helperText('Optional unit, e.g. km², wards.'),
            TextInput::make('icon')->maxLength(60),
            Textarea::make('source_note')->maxLength(255)->helperText('Internal verification note, e.g. 2022 census. Never shown as a production claim without approval.'),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            Select::make('is_active')->options([1 => 'Active', 0 => 'Inactive'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('label')->searchable(), TextColumn::make('value'), TextColumn::make('unit'), TextColumn::make('is_active')->badge()])->recordActions([
            EditAction::make(),
            Action::make('toggle')->requiresConfirmation()->action(fn (DistrictStatistic $record) => app(DistrictStatisticManager::class)->update(auth()->user(), $record, [...$record->only(['label', 'value', 'unit', 'icon', 'source_note', 'display_order']), 'is_active' => ! $record->is_active])),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDistrictStatistics::route('/'), 'create' => CreateDistrictStatistic::route('/create'), 'edit' => EditDistrictStatistic::route('/{record}/edit')];
    }
}
