<?php

namespace App\Filament\Resources\Projects;

use App\Domain\Cms\CouncilProjectManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Services\ServicesCluster;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Projects\Pages\CreateCouncilProject;
use App\Filament\Resources\Projects\Pages\EditCouncilProject;
use App\Filament\Resources\Projects\Pages\ListCouncilProjects;
use App\Models\CouncilProject;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class CouncilProjectResource extends Resource
{
    protected static ?string $model = CouncilProject::class;

    protected static ?string $cluster = ServicesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Projects';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project Overview')->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set): mixed => $set('slug', HandlesFeaturedImageUpload::slugFromTitle($state))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(160)
                    ->unique(ignoreRecord: true)
                    ->helperText('Suggested from title; you can edit it.'),
                Select::make('project_type')->options(['project' => 'Project', 'programme' => 'Programme'])->required(),
                Select::make('project_status')->options(['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On Hold', 'cancelled' => 'Cancelled'])->required(),
                TextInput::make('location')->maxLength(255),
                Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
                Select::make('wards')->multiple()->relationship('wards', 'name')->searchable()->helperText('All wards covered; supports multi-ward projects.'),
                TextInput::make('progress_percent')->numeric()->minValue(0)->maxValue(100)->helperText('Council-managed only; leave empty when unconfirmed.'),
                DatePicker::make('starts_at'),
                DatePicker::make('expected_completed_at'),
                DatePicker::make('completed_at'),
                TextInput::make('display_order')->numeric()->default(0)->required(),
                Textarea::make('summary')->maxLength(1000)->columnSpanFull(),
                MarkdownEditor::make('description')->required()->maxLength(50000)->columnSpanFull()->helperText('Formatting toolbar with live preview. Do not fabricate progress.'),
                Textarea::make('contact_instructions')->maxLength(5000)->columnSpanFull(),
            ])->columns(2),
            Section::make('Cover Image')->description('Upload a project image directly, or pick one from the library.')->schema([
                FileUpload::make('featured_image_upload')
                    ->label('Upload cover image')
                    ->image()
                    ->storeFiles(false)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(config('cms.max_upload_kb'))
                    ->helperText('JPEG, PNG or WebP. Saved to the media library on save.'),
                TextInput::make('featured_image_alt')
                    ->label('Image alt text')
                    ->maxLength(255),
                Select::make('featured_media_id')
                    ->label('Or pick library image')
                    ->relationship('featuredMedia', 'title', fn (Builder $query) => app(DataScopeAuthorizer::class)->apply($query->where('status', 'active')->where('mime_type', 'like', 'image/%'), auth()->user(), 'media.view', 'department_id', 'uploaded_by'))
                    ->searchable()
                    ->helperText('Active image from media library.'),
            ])->columns(2),
            Section::make('Associated Documents')->schema([
                Select::make('documents')->multiple()->relationship('documents', 'title')->searchable()->helperText('Optional documents linked to this project.'),
            ]),
            TranslationFields::make(CouncilProject::translatableFields()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('project_type')->badge(), TextColumn::make('project_status')->badge(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->filters([SelectFilter::make('project_status')->options(['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On Hold', 'cancelled' => 'Cancelled']), SelectFilter::make('project_type')->options(['project' => 'Project', 'programme' => 'Programme'])])->recordActions([DeleteAction::make(),
            EditAction::make(),
            Action::make('verify')->authorize(fn (CouncilProject $record): bool => Gate::allows('verify', $record))->visible(fn (CouncilProject $record): bool => $record->verification_status !== 'publishable')->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (CouncilProject $record): bool => Gate::allows('publish', $record))->visible(fn (CouncilProject $record): bool => $record->status !== 'published')->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (CouncilProject $record): bool => Gate::allows('publish', $record))->visible(fn (CouncilProject $record): bool => $record->status === 'published')->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (CouncilProject $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'projects.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListCouncilProjects::route('/'), 'create' => CreateCouncilProject::route('/create'), 'edit' => EditCouncilProject::route('/{record}/edit')];
    }
}
