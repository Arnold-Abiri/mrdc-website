<?php

namespace App\Filament\Resources\Officials;

use App\Domain\Cms\OfficialManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Resources\Officials\Pages\CreateOfficial;
use App\Filament\Resources\Officials\Pages\EditOfficial;
use App\Filament\Resources\Officials\Pages\ListOfficials;
use App\Models\Official;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class OfficialResource extends Resource
{
    protected static ?string $model = Official::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255), TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true), Textarea::make('biography')->maxLength(10000),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Select::make('photo_media_id')->relationship('photo', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'like', 'image/%'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))->searchable(),
            TextInput::make('display_order')->numeric()->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('title')->searchable(), TextColumn::make('department.name'), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (Official $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Official $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Official $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Official $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'officials.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListOfficials::route('/'), 'create' => CreateOfficial::route('/create'), 'edit' => EditOfficial::route('/{record}/edit')];
    }
}
