<?php

namespace App\Filament\Resources\ErrorEvents;

use App\Filament\Resources\ErrorEvents\Pages\ListErrorEvents;
use App\Filament\Resources\ErrorEvents\Pages\ViewErrorEvent;
use App\Models\ErrorEvent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class ErrorEventResource extends Resource
{
    protected static ?string $model = ErrorEvent::class;

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
            TextEntry::make('exception_class'),
            TextEntry::make('summary')->columnSpanFull(),
            TextEntry::make('route'),
            TextEntry::make('correlation_id'),
            TextEntry::make('resolved')->badge(),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->dateTime()->sortable(),
            TextColumn::make('exception_class')->searchable(),
            TextColumn::make('route'),
            TextColumn::make('resolved')->badge(),
        ])->filters([
            SelectFilter::make('resolved')->options([0 => 'Unresolved', 1 => 'Resolved']),
        ])->defaultSort('created_at', 'desc')->recordActions([
            ViewAction::make(),
            Action::make('resolve')->authorize(fn (ErrorEvent $record): bool => Gate::allows('update', $record))->requiresConfirmation()->action(fn (ErrorEvent $record) => $record->forceFill(['resolved' => true])->save()),
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
