<?php

namespace App\Filament\Resources\Projects;

use App\Domain\Cms\CouncilProjectManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Projects\Pages\CreateCouncilProject;
use App\Filament\Resources\Projects\Pages\EditCouncilProject;
use App\Filament\Resources\Projects\Pages\ListCouncilProjects;
use App\Models\CouncilProject;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class CouncilProjectResource extends Resource
{
    protected static ?string $model = CouncilProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Projects';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            Select::make('project_type')->options(['project' => 'Project', 'programme' => 'Programme'])->required(),
            Select::make('project_status')->options(['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On Hold', 'cancelled' => 'Cancelled'])->required(),
            TextInput::make('location')->maxLength(255),
            Textarea::make('summary')->maxLength(1000),
            Textarea::make('description')->required()->maxLength(50000)->helperText('Plain text only; HTML is not rendered. Do not fabricate progress.'),
            DatePicker::make('starts_at'), DatePicker::make('expected_completed_at'), DatePicker::make('completed_at'),
            TextInput::make('progress_percent')->numeric()->minValue(0)->maxValue(100)->helperText('Council-managed only; leave empty when unconfirmed.'),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            Select::make('wards')->multiple()->relationship('wards', 'name')->searchable()->helperText('All wards covered; supports multi-ward projects.'),
            Select::make('featured_media_id')->relationship('featuredMedia', 'title')->searchable()->helperText('Active image.'),
            Select::make('documents')->multiple()->relationship('documents', 'title')->searchable(),
            Textarea::make('contact_instructions')->maxLength(5000),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(CouncilProject::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('project_type')->badge(), TextColumn::make('project_status')->badge(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->filters([SelectFilter::make('project_status')->options(['planned' => 'Planned', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On Hold', 'cancelled' => 'Cancelled']), SelectFilter::make('project_type')->options(['project' => 'Project', 'programme' => 'Programme'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (CouncilProject $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (CouncilProject $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (CouncilProject $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (CouncilProject $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (CouncilProject $record) => app(CouncilProjectManager::class)->setStatus(auth()->user(), $record, 'archived')),
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
