<?php

namespace App\Filament\Resources\Services;

use App\Domain\Cms\ServiceManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            Textarea::make('summary')->maxLength(1000), Textarea::make('description')->maxLength(20000),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Repeater::make('requirements')->schema([Textarea::make('value')->required()])->simple(Textarea::make('value'))->maxItems(30),
            Repeater::make('steps')->schema([Textarea::make('value')->required()])->simple(Textarea::make('value'))->maxItems(30),
            Textarea::make('fees_information')->maxLength(5000)->helperText('Enter only verified fee information.'),
            TextInput::make('display_order')->numeric()->default(0)->required(), TextInput::make('seo_title')->maxLength(255), Textarea::make('meta_description')->maxLength(320),
            TranslationFields::make(Service::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('department.name'), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (Service $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Service $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Service $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Service $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'services.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListServices::route('/'), 'create' => CreateService::route('/create'), 'edit' => EditService::route('/{record}/edit')];
    }
}
