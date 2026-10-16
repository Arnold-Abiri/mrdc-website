<?php

namespace App\Filament\Resources\Investment\Pages;

use App\Filament\Resources\Investment\InvestmentOpportunityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvestmentOpportunities extends ListRecords
{
    protected static string $resource = InvestmentOpportunityResource::class;

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
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('opportunity_status', 'open'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('opportunity_status', 'open')->count())
                ->badgeColor('info'),
            'committed' => Tab::make('Committed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('opportunity_status', 'committed'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('opportunity_status', 'committed')->count())
                ->badgeColor('success'),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('opportunity_status', 'closed'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('opportunity_status', 'closed')->count())
                ->badgeColor('gray'),
        ];
    }
}
