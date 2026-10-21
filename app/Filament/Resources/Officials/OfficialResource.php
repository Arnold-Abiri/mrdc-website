<?php

namespace App\Filament\Resources\Officials;

use App\Domain\Cms\OfficialManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Council\CouncilCluster;
use App\Filament\Concerns\SlugField;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Officials\Pages\CreateOfficial;
use App\Filament\Resources\Officials\Pages\EditOfficial;
use App\Filament\Resources\Officials\Pages\ListOfficials;
use App\Models\Official;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class OfficialResource extends Resource
{
    protected static ?string $model = Official::class;

    protected static ?string $cluster = CouncilCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static \UnitEnum|string|null $navigationGroup = 'Council';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile Information')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(SlugField::autoFillHook()),
                TextInput::make('title')->label('Position / Role Title')->required()->maxLength(255),
                Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
                Select::make('is_department_head')->options([1 => 'Yes', 0 => 'No'])->required()->default(0)->helperText('Mark the head of the department. Shown on the public department page.'),
                TextInput::make('display_order')->numeric()->default(0)->required(),
                Textarea::make('biography')->maxLength(10000)->columnSpanFull(),
            ])->columns(2),
            Section::make('Official Portrait')->description('Upload an official portrait image directly, or pick one from the library.')->schema([
                FileUpload::make('photo_upload')
                    ->label('Upload portrait photo')
                    ->image()
                    ->storeFiles(false)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(config('cms.max_upload_kb'))
                    ->helperText('JPEG, PNG or WebP. Automatically stored in the media library on save.'),
                TextInput::make('photo_alt')
                    ->label('Photo alt text')
                    ->maxLength(255)
                    ->helperText('Accessible description, e.g. "Portrait of Councillor John Doe".'),
                Select::make('photo_media_id')
                    ->label('Or pick library image')
                    ->relationship('photo', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'like', 'image/%'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))
                    ->searchable()
                    ->helperText('Active photo from media library.'),
            ])->columns(2),
            SlugField::make('name'),
            TranslationFields::make(Official::translatableFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('photo.storage_path')->label('Photo')->disk(config('cms.media_disk'))->circular(),
            TextColumn::make('name')->searchable()->weight('semibold'),
            TextColumn::make('title')->searchable(),
            TextColumn::make('department.name'),
            TextColumn::make('status')->badge(),
            TextColumn::make('verification_status')->badge(),
        ])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([DeleteAction::make(),
            EditAction::make(),
            Action::make('verify')->authorize(fn (Official $record): bool => Gate::allows('verify', $record))->visible(fn (Official $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Official $record): bool => Gate::allows('publish', $record))->visible(fn (Official $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Official $record): bool => Gate::allows('publish', $record))->visible(fn (Official $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Official $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Official $record) => app(OfficialManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'officials.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListOfficials::route('/'), 'create' => CreateOfficial::route('/create'), 'edit' => EditOfficial::route('/{record}/edit')];
    }
}
