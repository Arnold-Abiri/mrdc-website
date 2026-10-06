<?php

namespace App\Filament\Resources\Investment\Pages;

use App\Filament\Resources\Investment\InvestmentOpportunityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvestmentOpportunities extends ListRecords
{
    protected static string $resource = InvestmentOpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
