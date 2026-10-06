<?php

namespace App\Filament\Resources\ProjectUpdates;

use App\Domain\Cms\ProjectUpdateManager;
use App\Filament\Resources\ProjectUpdates\Pages\CreateProjectUpdate;
use App\Filament\Resources\ProjectUpdates\Pages\EditProjectUpdate;
use App\Filament\Resources\ProjectUpdates\Pages\ListProjectUpdates;
use App\Models\ProjectUpdate;
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
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class ProjectUpdateResource extends Resource
{
    protected static ?string $model = ProjectUpdate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static ?string $navigationLabel = 'Project updates';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('council_project_id')->relationship('project', 'title')->searchable()->required(),
            DatePicker::make('update_date')->required(),
            TextInput::make('title')->required()->maxLength(255),
            Textarea::make('summary')->maxLength(1000),
            TextInput::make('progress_percent')->numeric()->minValue(0)->maxValue(100),
            Select::make('media_id')->relationship('media', 'title')->searchable(),
            TextInput::make('display_order')->numeric()->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('project.title')->label('Project')->searchable(), TextColumn::make('title')->searchable(), TextColumn::make('update_date')->date(), TextColumn::make('status')->badge()])->recordActions([
            EditAction::make(),
            Action::make('publish')->authorize(fn (ProjectUpdate $record): bool => Gate::allows('update', $record))->requiresConfirmation()->action(fn (ProjectUpdate $record) => app(ProjectUpdateManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (ProjectUpdate $record): bool => Gate::allows('update', $record))->requiresConfirmation()->action(fn (ProjectUpdate $record) => app(ProjectUpdateManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListProjectUpdates::route('/'), 'create' => CreateProjectUpdate::route('/create'), 'edit' => EditProjectUpdate::route('/{record}/edit')];
    }
}
