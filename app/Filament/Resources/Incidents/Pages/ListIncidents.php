<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Filament\Resources\Incidents\IncidentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->count())
                ->badgeColor('gray'),
            'open' => Tab::make('Open')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'open'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'open')->count())
                ->badgeColor('warning'),
            'monitoring' => Tab::make('Monitoring')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'monitoring'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'monitoring')->count())
                ->badgeColor('info'),
            'resolved' => Tab::make('Resolved')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'resolved'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'resolved')->count())
                ->badgeColor('success'),
        ];
    }
}
