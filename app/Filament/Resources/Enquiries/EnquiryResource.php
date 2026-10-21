<?php

namespace App\Filament\Resources\Enquiries;

use App\Domain\Cms\EnquiryManager;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Enquiries\EnquiriesCluster;
use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Filament\Resources\Enquiries\Pages\ViewEnquiry;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static ?string $cluster = EnquiriesCluster::class;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static \UnitEnum|string|null $navigationGroup = 'Public Enquiries';

    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('public_id')->label('Reference'),
            TextEntry::make('status')->badge(),
            TextEntry::make('submitted_at')->dateTime(),
            TextEntry::make('name'),
            TextEntry::make('email'),
            TextEntry::make('phone'),
            TextEntry::make('category'),
            TextEntry::make('organisation')->visible(fn (Enquiry $record): bool => $record->organisation !== null),
            TextEntry::make('context')->label('Origin / Ward')->state(function (Enquiry $record): ?string {
                if (! $record->context_type) {
                    return null;
                }
                if ($record->context_type === 'ward') {
                    return 'Ward: '.str($record->context_reference)->replace('-', ' ')->title();
                }

                return $record->context_type.':'.$record->context_reference;
            })->visible(fn (Enquiry $record): bool => $record->context_type !== null),
            TextEntry::make('department_name')->label('Department')->state(fn (Enquiry $record): ?string => $record->department instanceof Department ? $record->department->name : null),
            TextEntry::make('assignee_name')->label('Assigned to')->state(fn (Enquiry $record): ?string => $record->assignee instanceof User ? $record->assignee->name : null),
            TextEntry::make('subject'),
            TextEntry::make('message')->columnSpanFull(),
            RepeatableEntry::make('notes_list')
                ->label('Internal notes')
                ->state(fn (Enquiry $record): array => $record->notes()->with('author')->oldest()->get()->map(fn ($note): array => [
                    'meta' => $note->created_at->format('d M Y H:i').' · '.($note->author?->name ?? 'Staff'),
                    'body' => $note->body,
                ])->all())
                ->schema([
                    TextEntry::make('meta')->label('')->weight('bold'),
                    TextEntry::make('body')->label(''),
                ])
                ->columnSpanFull()
                ->visible(fn (Enquiry $record): bool => $record->notes()->exists()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No enquiries')
            ->emptyStateDescription('New messages from the public website will appear here.')
            ->emptyStateIcon(Heroicon::OutlinedEnvelope)
            ->columns([
                TextColumn::make('public_id')->label('Reference')->searchable(),
                TextColumn::make('subject')->searchable()->limit(50),
                TextColumn::make('department.name')->label('Department')->sortable(),
                TextColumn::make('category')->badge()->toggleable(),
                TextColumn::make('context_type')->label('Origin')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options([
                    'new' => 'New',
                    'in_progress' => 'In progress',
                    'resolved' => 'Resolved',
                    'closed' => 'Closed',
                ]),
                SelectFilter::make('category')->label('Category')->options([
                    'fault_report' => 'Fault Report',
                    'complaint' => 'Complaint',
                    'feedback' => 'Feedback',
                    'general' => 'General',
                    'services' => 'Services',
                    'service_enquiry' => 'Service Enquiry',
                    'investment_enquiry' => 'Investment Enquiry',
                    'other' => 'Other',
                ]),
                SelectFilter::make('department')->label('Department')->relationship('department', 'name'),
            ])
            ->defaultSort('submitted_at', 'desc')->recordUrl(fn (Enquiry $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                Action::make('quickResolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Enquiry $record): bool => ! in_array($record->status, ['resolved', 'closed'], true))
                    ->action(function (Enquiry $record): void {
                        app(EnquiryManager::class)->setStatus(auth()->user(), $record, 'resolved');
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->apply(parent::getEloquentQuery(), auth()->user(), 'enquiries.view', 'department_id', null);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnquiries::route('/'),
            'view' => ViewEnquiry::route('/{record}'),
        ];
    }
}
