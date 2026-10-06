<?php

namespace App\Filament\Resources\Slides;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Resources\Slides\Pages\CreateHomepageSlide;
use App\Filament\Resources\Slides\Pages\EditHomepageSlide;
use App\Filament\Resources\Slides\Pages\ListHomepageSlides;
use App\Models\HomepageSlide;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class HomepageSlideResource extends Resource
{
    protected static ?string $model = HomepageSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Homepage slides';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('headline')->required()->maxLength(255),
            Textarea::make('supporting_text')->maxLength(1000),
            TextInput::make('cta_label')->maxLength(120),
            TextInput::make('cta_url')->maxLength(2048)->helperText('Internal path only, e.g. /services.'),
            Select::make('media_id')->relationship('media', 'title')->searchable()->helperText('Active image; falls back to the default hero artwork.'),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            Select::make('is_active')->options([1 => 'Active', 0 => 'Inactive'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('headline')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('is_active')->badge(), TextColumn::make('display_order')])->recordActions([
            EditAction::make(),
            Action::make('publish')->authorize(fn (HomepageSlide $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (HomepageSlide $record) => app(HomepageSlideManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (HomepageSlide $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (HomepageSlide $record) => app(HomepageSlideManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListHomepageSlides::route('/'), 'create' => CreateHomepageSlide::route('/create'), 'edit' => EditHomepageSlide::route('/{record}/edit')];
    }
}
