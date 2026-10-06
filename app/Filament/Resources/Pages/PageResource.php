<?php

namespace App\Filament\Resources\Pages;

use App\Domain\Cms\PageManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
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

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            Textarea::make('summary')->maxLength(1000),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Repeater::make('blocks')->schema([
                Select::make('type')->options(['heading' => 'Heading', 'paragraph' => 'Paragraph', 'cta' => 'Link'])->required(),
                Textarea::make('text')->required()->maxLength(10000),
                TextInput::make('url')->helperText('Use an internal path beginning with / for links.'),
            ])->defaultItems(1)->required(),
            TextInput::make('seo_title')->maxLength(255),
            Textarea::make('meta_description')->maxLength(320),
            TranslationFields::make(Page::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(),
            TextColumn::make('slug')->searchable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('verification_status')->badge(),
            TextColumn::make('published_at')->dateTime(),
        ])->recordActions([
            EditAction::make(),
            Action::make('publish')->authorize(fn (Page $record): bool => Gate::allows('publish', $record))
                ->requiresConfirmation()->action(fn (Page $record) => app(PageManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Page $record): bool => Gate::allows('publish', $record))
                ->requiresConfirmation()->action(fn (Page $record) => app(PageManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('markVerified')->label('Mark verified')->authorize(fn (Page $record): bool => Gate::allows('verify', $record))
                ->requiresConfirmation()->action(fn (Page $record) => app(PageManager::class)->setVerification(auth()->user(), $record, 'verified')),
            Action::make('markPublishable')->label('Mark publishable')->authorize(fn (Page $record): bool => Gate::allows('verify', $record))
                ->requiresConfirmation()->action(fn (Page $record) => app(PageManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('archive')->authorize(fn (Page $record): bool => Gate::allows('publish', $record))
                ->requiresConfirmation()->action(fn (Page $record) => app(PageManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'pages.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
