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
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

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
            Section::make('Enquiry')
                ->columns(3)
                ->schema([
                    TextEntry::make('public_id')->label('Reference')->copyable(),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('submitted_at')->dateTime()->label('Submitted'),
                    TextEntry::make('category')->badge(),
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
                ]),
            Section::make('Citizen')
                ->columns(3)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable()->icon('heroicon-o-envelope'),
                    TextEntry::make('phone')->copyable()->icon('heroicon-o-phone'),
                    TextEntry::make('organisation')->visible(fn (Enquiry $record): bool => $record->organisation !== null),
                ]),
            Section::make('Message')
                ->schema([
                    TextEntry::make('subject')->weight('bold'),
                    TextEntry::make('message')->columnSpanFull(),
                ]),
            Section::make('Internal notes')
                ->schema([
                    RepeatableEntry::make('notes_list')
                        ->hiddenLabel()
                        ->state(fn (Enquiry $record): array => $record->notes()->with('author')->oldest()->get()->map(fn ($note): array => [
                            'meta' => $note->created_at->format('d M Y H:i').' · '.$note->author->name,
                            'body' => $note->body,
                        ])->all())
                        ->schema([
                            TextEntry::make('meta')->hiddenLabel()->weight('bold'),
                            TextEntry::make('body')->hiddenLabel()->copyable(),
                        ])
                        ->columnSpanFull(),
                ])
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
                Filter::make('overdue')->label('Overdue SLA')->query(fn (Builder $query): Builder => $query->overdue()),
            ])
            ->defaultSort('submitted_at', 'desc')->recordUrl(fn (Enquiry $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                Action::make('quickResolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Enquiry $record): bool => EnquiryManager::canTransition($record->status, 'resolved'))
                    ->action(function (Enquiry $record): void {
                        try {
                            app(EnquiryManager::class)->setStatus(auth()->user(), $record, 'resolved');
                        } catch (HttpException $exception) {
                            Notification::make()
                                ->title('Status no longer available')
                                ->body('This enquiry changed before Resolve was confirmed. The list has been refreshed.')
                                ->warning()
                                ->send();
                        }
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('changeStatus')
                        ->label('Change status')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->form([
                            Select::make('status')->label('New status')->options([
                                'in_progress' => 'In progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $updated = 0;
                            $skipped = 0;
                            foreach ($records as $record) {
                                if (! $record instanceof Enquiry || ! EnquiryManager::canTransition($record->status, $data['status'])) {
                                    $skipped++;

                                    continue;
                                }
                                try {
                                    app(EnquiryManager::class)->setStatus(auth()->user(), $record, $data['status']);
                                    $updated++;
                                } catch (Throwable) {
                                    $skipped++;
                                }
                            }
                            Notification::make()
                                ->title("Status updated for {$updated} enquir".($updated === 1 ? 'y' : 'ies').($skipped > 0 ? "; {$skipped} skipped (invalid transition or no permission)" : ''))
                                ->success()
                                ->send();
                        }),
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
        $overdue = (clone static::getEloquentQuery())->overdue()->count();
        if ($overdue > 0) {
            return (string) $overdue;
        }
        $count = static::getEloquentQuery()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdue = (clone static::getEloquentQuery())->overdue()->count();

        return $overdue > 0 ? 'danger' : 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnquiries::route('/'),
            'view' => ViewEnquiry::route('/{record}'),
        ];
    }
}
