<?php

namespace App\Filament\Resources\Projects\CouncilProjectResource\RelationManagers;

use App\Domain\Cms\ProjectUpdateManager;
use App\Filament\Concerns\TranslationFields;
use App\Models\CouncilProject;
use App\Models\ProjectUpdate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    protected static ?string $title = 'Milestones & Progress Updates';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('update_date')
                    ->default(now())
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('progress_percent')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->helperText('Overall project completion percentage at this milestone.'),
                TextInput::make('display_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Select::make('media_id')
                    ->relationship('media', 'title')
                    ->searchable()
                    ->helperText('Select photo from media library.'),
                Textarea::make('summary')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                TranslationFields::make(ProjectUpdate::translatableFields()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('update_date', 'desc')
            ->columns([
                TextColumn::make('update_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('progress_percent')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => filled($state) ? "{$state}%" : '-')
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'unpublished' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data): Model {
                        /** @var CouncilProject $owner */
                        $owner = $this->getOwnerRecord();
                        $data['council_project_id'] = $owner->getKey();

                        return app(ProjectUpdateManager::class)->create(auth()->user(), $data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function (ProjectUpdate $record, array $data): Model {
                        $data['council_project_id'] = $record->council_project_id;

                        return app(ProjectUpdateManager::class)->update(auth()->user(), $record, $data);
                    }),
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->authorize(fn (ProjectUpdate $record): bool => Gate::allows('update', $record))
                    ->visible(fn (ProjectUpdate $record): bool => $record->status !== 'published')
                    ->requiresConfirmation()
                    ->action(fn (ProjectUpdate $record) => app(ProjectUpdateManager::class)->setStatus(auth()->user(), $record, 'published')),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->authorize(fn (ProjectUpdate $record): bool => Gate::allows('update', $record))
                    ->visible(fn (ProjectUpdate $record): bool => $record->status === 'published')
                    ->requiresConfirmation()
                    ->action(fn (ProjectUpdate $record) => app(ProjectUpdateManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
