<?php

namespace App\Filament\Resources\Vacancies;

use App\Domain\Cms\VacancyManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Vacancies\Pages\CreateVacancy;
use App\Filament\Resources\Vacancies\Pages\EditVacancy;
use App\Filament\Resources\Vacancies\Pages\ListVacancies;
use App\Models\Vacancy;
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

class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Vacancies';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255), TextInput::make('slug')->required()->maxLength(160)->unique(ignoreRecord: true),
            TextInput::make('grade')->maxLength(60),
            TextInput::make('reference')->maxLength(60)->unique(ignoreRecord: true)->helperText('Optional HR reference, e.g. MRDC/HR/2026/03.'),
            Select::make('employment_type')->options(['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'internship' => 'Internship']),
            Textarea::make('description')->required()->maxLength(50000)->helperText('Plain text only; HTML is not rendered.'),
            Textarea::make('responsibilities')->maxLength(20000), Textarea::make('requirements')->maxLength(20000),
            DatePicker::make('opens_at'), DatePicker::make('closes_at')->helperText('Past closing dates read as closed on the public site. Expired vacancies must not be republished as open.'),
            Textarea::make('application_instructions')->maxLength(5000),
            Select::make('document_id')->relationship('document', 'title')->searchable()->helperText('Downloadable vacancy advert.'),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(Vacancy::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('grade'), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge(), TextColumn::make('closes_at')->date()])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'unpublished' => 'Unpublished', 'archived' => 'Archived'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (Vacancy $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (Vacancy $record) => app(VacancyManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (Vacancy $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Vacancy $record) => app(VacancyManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (Vacancy $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Vacancy $record) => app(VacancyManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (Vacancy $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (Vacancy $record) => app(VacancyManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'vacancies.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListVacancies::route('/'), 'create' => CreateVacancy::route('/create'), 'edit' => EditVacancy::route('/{record}/edit')];
    }
}
