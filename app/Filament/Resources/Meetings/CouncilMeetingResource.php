<?php

namespace App\Filament\Resources\Meetings;

use App\Domain\Cms\CouncilMeetingManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Meetings\Pages\CreateCouncilMeeting;
use App\Filament\Resources\Meetings\Pages\EditCouncilMeeting;
use App\Filament\Resources\Meetings\Pages\ListCouncilMeetings;
use App\Models\CouncilMeeting;
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

class CouncilMeetingResource extends Resource
{
    protected static ?string $model = CouncilMeeting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static \UnitEnum|string|null $navigationGroup = 'Council';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Council meetings';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            Select::make('meeting_type')->options(['full_council' => 'Full council', 'committee' => 'Committee', 'special' => 'Special', 'public_hearing' => 'Public hearing'])->required(),
            DatePicker::make('scheduled_date')->required(),
            TextInput::make('scheduled_time')->maxLength(20)->helperText('e.g. 10:00 CAT.'),
            TextInput::make('venue')->maxLength(255),
            Select::make('meeting_status')->options(['scheduled' => 'Scheduled', 'completed' => 'Completed', 'postponed' => 'Postponed', 'cancelled' => 'Cancelled'])->required(),
            Textarea::make('summary')->maxLength(5000),
            Select::make('agenda_document_id')->relationship('agenda', 'title')->searchable()->helperText('Published agenda only; drafts stay private.'),
            Select::make('minutes_document_id')->relationship('minutes', 'title')->searchable()->helperText('Published minutes only; drafts stay private.'),
            Select::make('department_id')->relationship('department', 'name')->searchable()->preload(),
            TextInput::make('display_order')->numeric()->default(0)->required(),
            TranslationFields::make(CouncilMeeting::translatableFields()), ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('meeting_type')->badge(), TextColumn::make('scheduled_date')->date(), TextColumn::make('meeting_status')->badge(), TextColumn::make('status')->badge(), TextColumn::make('verification_status')->badge()])->filters([SelectFilter::make('meeting_type')->options(['full_council' => 'Full council', 'committee' => 'Committee', 'special' => 'Special', 'public_hearing' => 'Public hearing']), SelectFilter::make('meeting_status')->options(['scheduled' => 'Scheduled', 'completed' => 'Completed', 'postponed' => 'Postponed', 'cancelled' => 'Cancelled'])])->recordActions([
            EditAction::make(),
            Action::make('verify')->authorize(fn (CouncilMeeting $record): bool => Gate::allows('verify', $record))->requiresConfirmation()->action(fn (CouncilMeeting $record) => app(CouncilMeetingManager::class)->setVerification(auth()->user(), $record, 'publishable')),
            Action::make('publish')->authorize(fn (CouncilMeeting $record): bool => Gate::allows('publish', $record))->disabled(fn (CouncilMeeting $record): bool => $record->verification_status !== 'publishable')->tooltip(fn (CouncilMeeting $record): ?string => $record->verification_status !== 'publishable' ? 'Verify this meeting before publishing it.' : null)->requiresConfirmation()->action(fn (CouncilMeeting $record) => app(CouncilMeetingManager::class)->setStatus(auth()->user(), $record, 'published')),
            Action::make('unpublish')->authorize(fn (CouncilMeeting $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (CouncilMeeting $record) => app(CouncilMeetingManager::class)->setStatus(auth()->user(), $record, 'unpublished')),
            Action::make('archive')->authorize(fn (CouncilMeeting $record): bool => Gate::allows('publish', $record))->requiresConfirmation()->action(fn (CouncilMeeting $record) => app(CouncilMeetingManager::class)->setStatus(auth()->user(), $record, 'archived')),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'meetings.view', 'department_id', 'created_by');
    }

    public static function getPages(): array
    {
        return ['index' => ListCouncilMeetings::route('/'), 'create' => CreateCouncilMeeting::route('/create'), 'edit' => EditCouncilMeeting::route('/{record}/edit')];
    }
}
