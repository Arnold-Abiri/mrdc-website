<?php

namespace App\Filament\Resources\Wards;

use App\Domain\Cms\WardManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Resources\Wards\Pages\CreateWard;
use App\Filament\Resources\Wards\Pages\EditWard;
use App\Filament\Resources\Wards\Pages\ListWards;
use App\Models\Ward;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class WardResource extends Resource
{
    protected static ?string $model = Ward::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    protected static \UnitEnum|string|null $navigationGroup = 'Council';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(120)->unique(ignoreRecord: true), Textarea::make('description')->maxLength(5000), Textarea::make('boundaries_description')->maxLength(5000)->helperText('Authoritative boundaries have not been supplied as GIS data; do not invent them.'), TextInput::make('map_url')->maxLength(2048)->helperText('Optional council-supplied map link. Leave empty until GIS data is approved.'), TextInput::make('display_order')->numeric()->default(0)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('slug')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (Ward $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (Ward $record) => app(WardManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Ward $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Ward $record) => app(WardManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Ward $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Ward $record) => app(WardManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Ward $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Ward $record) => app(WardManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'wards.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListWards::route('/'), 'create' => CreateWard::route('/create'), 'edit' => EditWard::route('/{record}/edit')];
    }
}
