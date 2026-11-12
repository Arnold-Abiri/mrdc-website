<?php

namespace App\Filament\Resources\ErrorEvents;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Reports\ReportsCluster;
use App\Filament\Resources\ErrorEvents\Pages\ListErrorEvents;
use App\Filament\Resources\ErrorEvents\Pages\ViewErrorEvent;
use App\Models\ErrorEvent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ErrorEventResource extends Resource
{
    protected static ?string $model = ErrorEvent::class;

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->applyGlobalOnly(parent::getEloquentQuery(), auth()->user(), 'system-health.view');
    }

    protected static ?string $cluster = ReportsCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Monitoring';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Error events';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('system-health.view');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Exception Overview')
                ->description('System-level application exception and execution context.')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('created_at')
                            ->label('Timestamp')
                            ->dateTime()
                            ->fontFamily('mono'),

                        TextEntry::make('resolved')
                            ->label('Resolution Status')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Resolved' : 'Unresolved / Open'),

                        TextEntry::make('correlation_id')
                            ->label('Correlation ID')
                            ->fontFamily('mono')
                            ->copyable()
                            ->placeholder('—'),
                    ]),

                    TextEntry::make('exception_class')
                        ->label('Exception Class')
                        ->fontFamily('mono')
                        ->weight('bold')
                        ->copyable(),

                    TextEntry::make('route')
                        ->label('Target Route / URI')
                        ->fontFamily('mono')
                        ->placeholder('—'),
                ]),

            Section::make('Stack Trace & Error Summary')
                ->description('Detailed exception diagnostic information.')
                ->schema([
                    TextEntry::make('summary')
                        ->label('')
                        ->fontFamily('mono')
                        ->size('xs')
                        ->columnSpanFull()
                        ->placeholder('No stack trace or summary recorded.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable()
                    ->fontFamily('mono')
                    ->size('xs'),

                TextColumn::make('exception_class')
                    ->label('Exception Class')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('route')
                    ->label('Route / URI')
                    ->fontFamily('mono')
                    ->size('xs')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('resolved')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Resolved' : 'Open'),
            ])
            ->filters([
                SelectFilter::make('resolved')
                    ->label('Status')
                    ->options([
                        0 => 'Unresolved (Open)',
                        1 => 'Resolved',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->recordActions([
                ViewAction::make(),
                Action::make('resolve')
                    ->label('Mark Resolved')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (ErrorEvent $record): bool => ! $record->resolved)
                    ->authorize(fn (ErrorEvent $record): bool => Gate::allows('update', $record))
                    ->requiresConfirmation()
                    ->action(fn (ErrorEvent $record) => $record->forceFill(['resolved' => true])->save()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErrorEvents::route('/'),
            'view' => ViewErrorEvent::route('/{record}'),
        ];
    }
}
