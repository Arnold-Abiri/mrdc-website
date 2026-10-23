<?php

namespace App\Filament\Resources\Slides;

use App\Domain\Cms\HomepageSlideManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Content\ContentCluster;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Slides\Pages\CreateHomepageSlide;
use App\Filament\Resources\Slides\Pages\EditHomepageSlide;
use App\Filament\Resources\Slides\Pages\ListHomepageSlides;
use App\Models\HomepageSlide;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class HomepageSlideResource extends Resource
{
    protected static ?string $model = HomepageSlide::class;

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->applyGlobalOnly(parent::getEloquentQuery(), auth()->user(), 'slides.view');
    }

    protected static ?string $cluster = ContentCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Homepage slides';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Content')->schema([
                TextInput::make('headline')->required()->maxLength(120)->helperText('Keep under ~70 characters so the hero never wraps past 3 lines.'),
                Textarea::make('supporting_text')->maxLength(220)->rows(3)->helperText('Keep under ~160 characters so the hero never pushes content below the fold.'),
                TextInput::make('cta_label')->maxLength(40)->helperText('Short label, e.g. “Our services”.'),
                TextInput::make('cta_url')->maxLength(2048)->helperText('Internal path only, e.g. /services.'),
            ])->columns(2),
            Section::make('Hero Artwork / Background')->description('Upload a slide background image directly, or select one from the media library.')->schema([
                FileUpload::make('slide_image_upload')
                    ->label('Upload slide image')
                    ->image()
                    ->storeFiles(false)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(config('cms.max_upload_kb'))
                    ->helperText('JPEG, PNG or WebP. Automatically saved to the media library on save.'),
                TextInput::make('slide_image_alt')
                    ->label('Image alt text / description')
                    ->maxLength(255)
                    ->helperText('Short description for screen readers and accessibility.'),
                Select::make('media_id')
                    ->label('Or select existing library image')
                    ->relationship('media', 'title')
                    ->searchable()
                    ->helperText('Choose an active image. If neither is provided, default hero artwork is used.'),
            ])->columns(2),
            Section::make('Settings')->schema([
                TextInput::make('display_order')->numeric()->default(0)->required(),
                Select::make('is_active')->options([1 => 'Active', 0 => 'Inactive'])->default(1)->required()->helperText('Only active, published slides appear on the public homepage. Saving an edit returns a slide to draft; publish it again after review.'),
            ])->columns(2),
            TranslationFields::make(HomepageSlide::translatableFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('media.storage_path')->label('Image')->disk(config('cms.media_disk'))->square(),
            TextColumn::make('headline')->searchable()->weight('semibold'),
            TextColumn::make('status')->badge(),
            TextColumn::make('is_active')->badge(),
            TextColumn::make('display_order'),
        ])->recordActions([DeleteAction::make(),
            EditAction::make(),
            Action::make('publish')->authorize(fn (HomepageSlide $record): bool => Gate::allows('publish', $record))->visible(fn (HomepageSlide $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (HomepageSlide $record) => app(HomepageSlideManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (HomepageSlide $record): bool => Gate::allows('publish', $record))->visible(fn (HomepageSlide $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (HomepageSlide $record) => app(HomepageSlideManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListHomepageSlides::route('/'), 'create' => CreateHomepageSlide::route('/create'), 'edit' => EditHomepageSlide::route('/{record}/edit')];
    }
}
