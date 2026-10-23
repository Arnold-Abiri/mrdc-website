<?php

namespace App\Filament\Resources\Incidents;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Enquiries\EnquiriesCluster;
use App\Filament\Resources\Incidents\Pages\CreateIncident;
use App\Filament\Resources\Incidents\Pages\EditIncident;
use App\Filament\Resources\Incidents\Pages\ListIncidents;
use App\Models\Incident;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->applyGlobalOnly(parent::getEloquentQuery(), auth()->user(), 'system-health.view');
    }

    protected static ?string $cluster = EnquiriesCluster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static \UnitEnum|string|null $navigationGroup = 'Public Enquiries';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Incidents';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('system-health.view');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('started_at')->required(),
            DateTimePicker::make('recovered_at'),
            Select::make('source')->options(['manual' => 'Manual', 'health_check' => 'Health check', 'external_monitor' => 'External monitor'])->required()->default('manual'),
            Select::make('status')->options(['open' => 'Open', 'monitoring' => 'Monitoring', 'resolved' => 'Resolved'])->required()->default('open'),
            Textarea::make('summary')->maxLength(500)->helperText('Safe operational summary only. No credentials or hostnames.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('started_at')->dateTime()->sortable(),
            TextColumn::make('recovered_at')->dateTime(),
            TextColumn::make('source')->badge(),
            TextColumn::make('status')->badge(),
        ])->filters([
            SelectFilter::make('status')->options(['open' => 'Open', 'monitoring' => 'Monitoring', 'resolved' => 'Resolved']),
        ])->defaultSort('started_at', 'desc')->recordActions([DeleteAction::make(),
            EditAction::make(),
        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'edit' => EditIncident::route('/{record}/edit'),
        ];
    }
}
