<?php

namespace App\Filament\Resources\Galleries;

use App\Domain\Cms\GalleryManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Models\Gallery;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $cluster = ContentCluster::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Photo galleries';

    protected static ?string $modelLabel = 'gallery';

    protected static ?string $pluralModelLabel = 'photo galleries';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true)->helperText('Lowercase letters, numbers and single hyphens, e.g. ward-5-road-rehabilitation.'),
            Textarea::make('summary')->maxLength(500)->helperText('Short line shown on the gallery card.'),
            Textarea::make('description')->maxLength(20000)->helperText('Plain text only; HTML is not rendered.'),
            TextInput::make('location')->maxLength(255),
            DatePicker::make('album_date')->label('Date photographed'),
            Select::make('cover_media_id')
                ->label('Cover image')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => self::imageSearch($search))
                ->getOptionLabelUsing(fn ($value): ?string => Media::query()->whereKey($value)->value('title'))
                ->helperText('Optional. The first album image is used as the cover when empty.'),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Repeater::make('media')
                ->label('Album images')
                ->schema([
                    Select::make('media_id')
                        ->label('Image')
                        ->required()
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => self::imageSearch($search))
                        ->getOptionLabelUsing(fn ($value): ?string => Media::query()->whereKey($value)->value('title')),
                    TextInput::make('caption')->maxLength(500)->helperText('Optional caption shown under the image.'),
                ])
                ->afterStateHydrated(function (Repeater $component, ?Gallery $record): void {
                    if (! $record instanceof Gallery) {
                        return;
                    }
                    $component->state($record->media->map(fn ($media): array => [
                        'media_id' => $media->id,
                        'caption' => $media->pivot->caption,
                    ])->all());
                })
                ->reorderable()
                ->addActionLabel('Add image')
                ->defaultItems(1)
                ->maxItems(60)
                ->required(),
            Select::make('is_featured')->options([1 => 'Yes', 0 => 'No'])->default(0)->required()->helperText('Featured galleries appear first on the public gallery page.'),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(Gallery::translatableFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->weight('semibold'),
            TextColumn::make('media_count')->label('Images')->badge(),
            TextColumn::make('album_date')->label('Date')->date(),
            TextColumn::make('location')->searchable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('is_featured')->label('Featured')->badge()->formatStateUsing(fn (bool|int $state): string => $state ? 'Featured' : 'Standard')->color(fn (bool|int $state): string => $state ? 'warning' : 'gray'),
            TextColumn::make('status')->badge(),
            TextColumn::make('verification_status')->badge(),
            TextColumn::make('published_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived']),
            SelectFilter::make('verification_status')->options(['demo' => 'Demo', 'verified' => 'Verified', 'publishable' => 'Publishable']),
            SelectFilter::make('is_featured')->label('Featured')->options([1 => 'Featured', 0 => 'Standard']),
        ])->recordActions([DeleteAction::make(),
            EditAction::make(),
            Action::make('verify')->label('Mark publishable')->authorize(fn (Gallery $record): bool => Gate::allows('verify', $record))->visible(fn (Gallery $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (Gallery $record) => app(GalleryManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Gallery $record): bool => Gate::allows('publish', $record))->visible(fn (Gallery $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (Gallery $record) => app(GalleryManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Gallery $record): bool => Gate::allows('publish', $record))->visible(fn (Gallery $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (Gallery $record) => app(GalleryManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Gallery $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Gallery $record) => app(GalleryManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'galleries.view', 'department_id', 'created_by');

        return $query->withCount('media');
    }

    public static function getPages(): array
    {
        return ['index' => ListGalleries::route('/'), 'create' => CreateGallery::route('/create'), 'edit' => EditGallery::route('/{record}/edit')];
    }

    /**
     * Scoped, searchable list of active album images (id => title).
     *
     * @return array<int, string>
     */
    private static function imageSearch(string $search): array
    {
        return app(DataScopeAuthorizer::class)
            ->apply(Media::query()->where('status', 'active')->where('mime_type', 'like', 'image/%'), auth()->user(), 'media.view', 'department_id', 'uploaded_by')
            ->where('title', 'like', '%'.$search.'%')
            ->orderBy('title')
            ->limit(50)
            ->pluck('title', 'id')
            ->all();
    }
}
