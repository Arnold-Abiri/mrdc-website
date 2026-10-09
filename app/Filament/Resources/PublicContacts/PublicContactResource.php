<?php

namespace App\Filament\Resources\PublicContacts;

use App\Domain\Cms\PublicContactManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Resources\PublicContacts\Pages\CreatePublicContact;
use App\Filament\Resources\PublicContacts\Pages\EditPublicContact;
use App\Filament\Resources\PublicContacts\Pages\ListPublicContacts;
use App\Models\PublicContact;
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

class PublicContactResource extends Resource
{
    protected static ?string $model = PublicContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;
    protected static \UnitEnum|string|null $navigationGroup = 'Public Enquiries';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Public contacts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('office')->required()->maxLength(255), Select::make('type')->options(['phone' => 'Phone', 'email' => 'Email', 'physical_address' => 'Physical address', 'postal_address' => 'Postal address'])->required(), Textarea::make('value')->required()->maxLength(2000), Select::make('department_id')->relationship('department', 'name')->searchable()->preload(), TextInput::make('display_order')->numeric()->default(0)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('office')->searchable(), TextColumn::make('type')->badge(), TextColumn::make('department.name'), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge(), TextColumn::make('is_public')->formatStateUsing(fn (bool $state): string => $state ? 'Public' : 'Private')->badge()])->filters([SelectFilter::make('type')->options(['phone' => 'Phone', 'email' => 'Email', 'physical_address' => 'Physical address', 'postal_address' => 'Postal address']), SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (PublicContact $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (PublicContact $record) => app(PublicContactManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (PublicContact $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (PublicContact $record) => app(PublicContactManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (PublicContact $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (PublicContact $record) => app(PublicContactManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (PublicContact $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (PublicContact $record) => app(PublicContactManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'contacts.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListPublicContacts::route('/'), 'create' => CreatePublicContact::route('/create'), 'edit' => EditPublicContact::route('/{record}/edit')];
    }
}
