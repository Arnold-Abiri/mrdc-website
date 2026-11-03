<?php

namespace App\Filament\Resources\Media;

use App\Domain\Cms\MediaManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $cluster = ContentCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Placeholder::make('current_media_preview')
                ->label('Current file')
                ->content(fn (?Media $record): HtmlString|string => $record === null
                    ? 'No file selected.'
                    : (str_starts_with($record->mime_type, 'image/')
                        ? new HtmlString('<img src="'.e(route('admin.media.file', $record->id)).'" alt="'.e($record->title).'" style="max-height: 220px; max-width: 100%; object-fit: contain;">')
                        : new HtmlString('<a href="'.e(route('admin.media.preview', $record->id)).'" target="_blank" rel="noopener">'.e($record->original_filename).'</a>')))
                ->html()
                ->visible(fn (?Media $record): bool => $record !== null),
            FileUpload::make('file')->visibleOn('create')->required()->storeFiles(false)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])->maxSize(config('cms.max_upload_kb')),
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('alt_text')->maxLength(255),
            Textarea::make('caption')->maxLength(2000),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            TranslationFields::make(Media::translatableFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ViewColumn::make('preview')
                ->label('File')
                ->view('filament.tables.columns.media-preview'),
            TextColumn::make('title')->searchable()->limit(30)->tooltip(fn (Media $record): string => $record->title), TextColumn::make('mime_type'), TextColumn::make('size'),
            TextColumn::make('original_filename')->label('Filename')->searchable()->toggleable(),
            TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime(),
            TextColumn::make('alt_text')->label('Alt text')->state(fn (Media $record): string => str_starts_with($record->mime_type, 'image/') ? ($record->alt_text !== null && trim($record->alt_text) !== '' ? 'Provided' : 'Missing') : 'N/A')->badge()->color(fn (string $state): string => $state === 'Missing' ? 'danger' : ($state === 'Provided' ? 'success' : 'gray')),
        ])->recordActions([
            Action::make('preview')
                ->label('Preview')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->url(fn (Media $record): string => route('admin.media.preview', $record->id))
                ->openUrlInNewTab(),
            EditAction::make(),
            Action::make('archive')->authorize(fn (Media $record): bool => Gate::allows('update', $record))
                ->requiresConfirmation()->action(fn (Media $record) => app(MediaManager::class)->archive(auth()->user(), $record)),
            DeleteAction::make(),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'media.view', 'department_id', 'uploaded_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListMedia::route('/'), 'create' => CreateMedia::route('/create'), 'edit' => EditMedia::route('/{record}/edit')];
    }
}
