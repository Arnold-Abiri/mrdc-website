<?php

namespace App\Filament\Resources\Services;

use App\Domain\Cms\ServiceManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Services\ServicesCluster;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Concerns\SlugField;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Concerns\TranslationStatusColumn;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
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

    protected static ?string $cluster = ServicesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(SlugField::autoFillHook()),
            Textarea::make('summary')->maxLength(1000)->rows(3)->helperText('One or two sentences shown on cards and search results.'), MarkdownEditor::make('description')->maxLength(20000)->columnSpanFull()->helperText('Formatting toolbar with live preview. Plain paragraphs, lists and links only.'),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Repeater::make('requirements')->schema([Textarea::make('value')->required()])->simple(Textarea::make('value'))->maxItems(30),
            Repeater::make('steps')->schema([Textarea::make('value')->required()])->simple(Textarea::make('value'))->maxItems(30),
            Textarea::make('fees_information')->maxLength(5000)->helperText('Enter only verified fee information.'),
            TextInput::make('display_order')->numeric()->default(0)->required(), TextInput::make('seo_title')->maxLength(255), Textarea::make('meta_description')->maxLength(320),
            SlugField::make('name'),
            TranslationFields::make(Service::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('department.name'),
                TextColumn::make('status')->badge(),
                TextColumn::make('verification_status')->badge(),
                TranslationStatusColumn::make(),
            ])
            ->recordActions([
                PreviewHelper::makePreviewAction(),
                DeleteAction::make(),
                EditAction::make(),
                Action::make('verify')->authorize(fn (Service $record): bool => Gate::allows('verify', $record))->visible(fn (Service $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setVerification(auth()->user(), $record, 'publishable')),
                Action::make('publish')->authorize(fn (Service $record): bool => Gate::allows('publish', $record))->visible(fn (Service $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setStatus(auth()->user(), $record, 'published')),
                Action::make('unpublish')->authorize(fn (Service $record): bool => Gate::allows('publish', $record))->visible(fn (Service $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
                Action::make('archive')->authorize(fn (Service $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Service $record) => app(ServiceManager::class)->setStatus(auth()->user(), $record, 'archived')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
