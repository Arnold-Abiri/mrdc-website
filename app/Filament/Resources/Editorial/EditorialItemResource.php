<?php

namespace App\Filament\Resources\Editorial;

use App\Domain\Cms\EditorialManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Editorial\Pages\CreateEditorialItem;
use App\Filament\Resources\Editorial\Pages\EditEditorialItem;
use App\Filament\Resources\Editorial\Pages\ListEditorialItems;
use App\Models\EditorialItem;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class EditorialItemResource extends Resource
{
    protected static ?string $model = EditorialItem::class;

    protected static ?string $cluster = ContentCluster::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'News and notices';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Story')->description('The headline, text and classification. The web address is suggested from the title.')->schema([
                Select::make('type')->options(['news' => 'News', 'notice' => 'Notice'])->required(),
                TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set): mixed => $set('slug', HandlesFeaturedImageUpload::slugFromTitle($state))),
                TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true)->helperText('Suggested from the title; you can edit it.'),
                Textarea::make('summary')->maxLength(1000)->rows(3)->helperText('One or two sentences shown on listing cards and search results.'),
                MarkdownEditor::make('body')->required()->maxLength(50000)->columnSpanFull()->helperText('Formatting toolbar with live preview. Headings, bold, lists and links only — scripts and embeds are stripped on the public site.'),
                TextInput::make('category')->maxLength(60), Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            ])->columns(2),
            Section::make('Cover image')->description('Upload here, or pick an existing library image below. Uploading replaces the library pick.')->schema([
                FileUpload::make('featured_image_upload')->label('Upload cover image')->image()->storeFiles(false)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(config('cms.max_upload_kb'))->helperText('JPEG, PNG or WebP. Saved to the Media library automatically.'),
                TextInput::make('featured_image_alt')->label('Image alt text')->maxLength(255)->helperText('Short description for screen readers.'),
                Textarea::make('featured_image_caption')->label('Image caption')->maxLength(2000)->helperText('Visible credit line under the image.'),
                Select::make('featured_media_id')->label('Or pick a library image')->relationship('featuredMedia', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'like', 'image/%'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))->searchable(),
            ])->columns(2),
            Section::make('Publishing')->description('Expiry, urgency, search and ordering.')->schema([
                Select::make('documents')->multiple()->relationship('documents', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'published'), auth()->user(), 'documents.view', 'department_id', 'created_by'))->searchable(),
                DatePicker::make('expires_at')->visible(fn (callable $get): bool => $get('type') === 'notice')->helperText('Expired notices are excluded from public pages and search.'),
                Select::make('is_urgent')->options([1 => 'Yes', 0 => 'No'])->required()->default(0)->helperText('Urgent notices appear in the public alert banner until they expire or are unpublished.'),
                TextInput::make('seo_title')->maxLength(255), TextInput::make('meta_description')->maxLength(320),
                TextInput::make('display_order')->numeric()->default(0)->required(),
            ])->columns(2),
            TranslationFields::make(EditorialItem::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No stories yet')
            ->emptyStateDescription('Create your first news article or notice — it will appear here once saved as a draft.')
            ->emptyStateIcon(Heroicon::OutlinedNewspaper)
            ->columns([TextColumn::make('title')->searchable(), TextColumn::make('type')->badge(), TextColumn::make('category')->searchable()->toggleable(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()->toggleable(), TextColumn::make('published_at')->dateTime()->sortable()->toggleable()])->filters([SelectFilter::make('type')->options(['news' => 'News', 'notice' => 'Notice']), SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([DeleteAction::make(),
                EditAction::make(),
                Action::make('verify')->authorize(fn (EditorialItem $record): bool => Gate::allows('verify', $record))->visible(fn (EditorialItem $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setVerification(auth()->user(), $record, 'publishable')),
                Action::make('publish')->authorize(fn (EditorialItem $record): bool => Gate::allows('publish', $record))->visible(fn (EditorialItem $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setStatus(auth()->user(), $record, 'published')),
                Action::make('verifyAndPublish')->label('Verify & Publish')->authorize(fn (EditorialItem $record): bool => Gate::allows('verify', $record) && Gate::allows('publish', $record))->visible(fn (EditorialItem $record): bool => $record->status !== 'published')->requiresConfirmation()->action(function (EditorialItem $record): void {
                    $manager = app(EditorialManager::class);
                    $actor = auth()->user();
                    if ($record->verification_status !== 'publishable') {
                        $manager->setVerification($actor, $record, 'publishable');
                    }
                    $manager->setStatus($actor, $record->fresh(), 'published');
                }),
                Action::make('unpublish')->authorize(fn (EditorialItem $record): bool => Gate::allows('publish', $record))->visible(fn (EditorialItem $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
                Action::make('archive')->authorize(fn (EditorialItem $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setStatus(auth()->user(), $record, 'archived')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'editorial.view', 'department_id', 'created_by');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', 'draft')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return ['index' => ListEditorialItems::route('/'), 'create' => CreateEditorialItem::route('/create'), 'edit' => EditEditorialItem::route('/{record}/edit')];
    }
}
