<?php

namespace App\Filament\Resources\Media;

use App\Domain\Cms\MediaManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
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

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('file')->visibleOn('create')->required()->storeFiles(false)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])->maxSize(config('cms.max_upload_kb')),
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('alt_text')->maxLength(255),
            Textarea::make('caption')->maxLength(2000),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(), TextColumn::make('mime_type'), TextColumn::make('size'),
            TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime(),
        ])->recordActions([
            EditAction::make(),
            Action::make('archive')->authorize(fn (Media $record): bool => Gate::allows('update', $record))
                ->requiresConfirmation()->action(fn (Media $record) => app(MediaManager::class)->archive(auth()->user(), $record)),
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
