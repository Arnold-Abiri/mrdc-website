<?php

namespace App\Filament\Resources\Documents;

use App\Domain\Cms\DocumentManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            Textarea::make('description')->maxLength(5000),
            Select::make('category')->options(array_combine(['policy', 'report', 'plan', 'budget', 'form', 'notice', 'minutes', 'publication', 'other'], ['Policy', 'Report', 'Plan', 'Budget', 'Form', 'Notice', 'Minutes', 'Publication', 'Other']))->required(),
            Select::make('media_id')->relationship('media', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'application/pdf'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))->searchable()->required(),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Select::make('visibility')->options(['public' => 'Public', 'private' => 'Private'])->required()->default('public'),
            DatePicker::make('reference_date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(), TextColumn::make('category')->badge(),
            TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge(),
            TextColumn::make('published_at')->dateTime(),
        ])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (Document $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Document $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Document $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Document $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'documents.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListDocuments::route('/'), 'create' => CreateDocument::route('/create'), 'edit' => EditDocument::route('/{record}/edit')];
    }
}
