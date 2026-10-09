<?php

namespace App\Filament\Resources\Editorial;

use App\Domain\Cms\EditorialManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Editorial\Pages\CreateEditorialItem;
use App\Filament\Resources\Editorial\Pages\EditEditorialItem;
use App\Filament\Resources\Editorial\Pages\ListEditorialItems;
use App\Models\EditorialItem;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class EditorialItemResource extends Resource
{
    protected static ?string $model = EditorialItem::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'News and notices';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->options(['news' => 'News', 'notice' => 'Notice'])->required(),
            TextInput::make('title')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            Textarea::make('summary')->maxLength(1000), Textarea::make('body')->required()->maxLength(50000)->helperText('Plain text only; HTML is not rendered.'),
            TextInput::make('category')->maxLength(60), Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Select::make('featured_media_id')->relationship('featuredMedia', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'like', 'image/%'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))->searchable(),
            Select::make('documents')->multiple()->relationship('documents', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'published'), auth()->user(), 'documents.view', 'department_id', 'created_by'))->searchable(),
            DatePicker::make('expires_at')->visible(fn (callable $get): bool => $get('type') === 'notice')->helperText('Expired notices are excluded from public pages and search.'),
            Select::make('is_urgent')->options([1 => 'Yes', 0 => 'No'])->required()->default(0)->helperText('Urgent notices appear in the public alert banner until they expire or are unpublished.'),
            TextInput::make('seo_title')->maxLength(255), TextInput::make('meta_description')->maxLength(320),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(EditorialItem::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('type')->badge(), TextColumn::make('category')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge(), TextColumn::make('published_at')->dateTime()])->filters([SelectFilter::make('type')->options(['news' => 'News', 'notice' => 'Notice']), SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (EditorialItem $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (EditorialItem $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (EditorialItem $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (EditorialItem $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (EditorialItem $record) => app(EditorialManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'editorial.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListEditorialItems::route('/'), 'create' => CreateEditorialItem::route('/create'), 'edit' => EditEditorialItem::route('/{record}/edit')];
    }
}
