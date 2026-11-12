<?php

namespace App\Filament\Resources\Projects\CouncilProjectResource\RelationManagers;

use App\Domain\Cms\ProjectUpdateManager;
use App\Domain\Content\TranslationManager;
use App\Filament\Concerns\HandlesTranslations;
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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

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
                Placeholder::make('current_update_image')
                    ->label('Current image')
                    ->content(fn (?ProjectUpdate $record): HtmlString|string => $record?->media_id
                        ? new HtmlString('<a href="'.e(route('admin.media.preview', $record->media_id)).'" target="_blank" rel="noopener"><img src="'.e(route('admin.media.file', $record->media_id)).'" alt="Current project update image" style="max-height: 180px; max-width: 100%; object-fit: contain; border-radius: 8px; border: 1px solid #E2E8F0;"></a>')
                        : 'No image selected.')
                    ->html()
                    ->visible(fn (?ProjectUpdate $record): bool => $record !== null),
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

                        $record = app(ProjectUpdateManager::class)->create(auth()->user(), $data);
                        $this->saveTranslationsFromData($record, $data);

                        return $record;
                    }),
            ])
            ->recordActions([
                HandlesTranslations::makeAiTranslateAction(),
                EditAction::make()
                    ->using(function (ProjectUpdate $record, array $data): Model {
                        $data['council_project_id'] = $record->council_project_id;

                        $updated = app(ProjectUpdateManager::class)->update(auth()->user(), $record, $data);
                        $this->saveTranslationsFromData($updated, $data);

                        return $updated;
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

    private function saveTranslationsFromData(Model $record, array $data): void
    {
        $input = ['sn' => [], 'nd' => []];
        foreach ($data as $key => $value) {
            if (! str_starts_with((string) $key, 'content_translations_')) {
                continue;
            }
            $rest = substr((string) $key, strlen('content_translations_'));
            $locale = str_starts_with($rest, 'sn_') ? 'sn' : (str_starts_with($rest, 'nd_') ? 'nd' : null);
            if ($locale === null) {
                continue;
            }
            $input[$locale][substr($rest, 3)] = is_string($value) ? $value : null;
        }
        $input = array_filter($input);
        if ($input === []) {
            return;
        }
        app(TranslationManager::class)->saveTranslations(auth()->user(), $record, $input);
    }
}
