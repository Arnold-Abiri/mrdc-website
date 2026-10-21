<?php

namespace App\Filament\Resources\Documents;

use App\Domain\Cms\DocumentManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Concerns\SlugField;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Concerns\TranslationStatusColumn;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $cluster = ContentCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Details')->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(SlugField::autoFillHook()),
                Textarea::make('description')->maxLength(5000)->columnSpanFull(),
                Select::make('category')->options(collect(Document::CATEGORIES)->mapWithKeys(fn (string $category): array => [$category => ucwords(str_replace('_', ' ', $category))])->all())->required(),
                Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
                Select::make('visibility')->options(['public' => 'Public', 'private' => 'Private'])->required()->default('public'),
                DatePicker::make('reference_date'),
            ])->columns(2),
            Section::make('Document File')->description('Upload a PDF directly, or pick one already in the library.')->schema([
                FileUpload::make('document_file_upload')
                    ->label('Upload PDF document')
                    ->storeFiles(false)
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(config('cms.max_upload_kb'))
                    ->helperText('PDF file. Automatically created in the Media library on save.'),
                Select::make('media_id')
                    ->label('Or select existing PDF from library')
                    ->relationship('media', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'application/pdf'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))
                    ->searchable()
                    ->helperText('Required if no file is uploaded above.'),
            ])->columns(2),
            SlugField::make('title'),
            TranslationFields::make(Document::translatableFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('category')->badge(),
            TextColumn::make('file_size')
                ->label('Size')
                ->state(function (Document $record): string {
                    $bytes = $record->media?->size ?? 0;
                    if ($bytes >= 1048576) {
                        return number_format($bytes / 1048576, 1).' MB';
                    }
                    if ($bytes >= 1024) {
                        return number_format($bytes / 1024, 0).' KB';
                    }

                    return $bytes > 0 ? $bytes.' B' : '-';
                })
                ->badge()
                ->color('gray')
                ->toggleable(),
            TextColumn::make('download_count')
                ->label('Downloads')
                ->numeric()
                ->sortable()
                ->toggleable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('verification_status')->badge(),
            TranslationStatusColumn::make(),
            TextColumn::make('published_at')->dateTime()->sortable(),
        ])->recordActions([
            PreviewHelper::makePreviewAction(),
            DeleteAction::make(),
            EditAction::make(),
            Action::make('replace')->label('Replace file')->authorize(fn (Document $record): bool => Gate::allows('update', $record))->schema([Select::make('media_id')->label('Replacement PDF')->relationship('media', 'title')->searchable()->required()->helperText('The previous file is preserved as a version; the replacement returns the document to draft for review.')])->action(fn (Document $record, array $data) => app(DocumentManager::class)->replace(auth()->user(), $record, (int) $data['media_id'])),
            Action::make('verify')->authorize(fn (Document $record): bool => Gate::allows('verify', $record))->visible(fn (Document $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Document $record): bool => Gate::allows('publish', $record))->visible(fn (Document $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Document $record): bool => Gate::allows('publish', $record))->visible(fn (Document $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Document $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Document $record) => app(DocumentManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery()->with('media'), auth()->user(), 'documents.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListDocuments::route('/'), 'create' => CreateDocument::route('/create'), 'edit' => EditDocument::route('/{record}/edit')];
    }
}
