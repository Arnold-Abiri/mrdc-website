<?php

namespace App\Filament\Resources\Investment\Pages;

use App\Domain\Cms\InvestmentManager;
use App\Filament\Resources\Investment\InvestmentOpportunityResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvestmentOpportunity extends CreateRecord
{
    protected static string $resource = InvestmentOpportunityResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(InvestmentManager::class)->create(auth()->user(), $data);
    }
}
