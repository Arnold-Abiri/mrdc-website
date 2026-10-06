<?php

namespace App\Filament\Resources\Investment\Pages;

use App\Domain\Cms\InvestmentManager;
use App\Filament\Resources\Investment\InvestmentOpportunityResource;
use App\Models\InvestmentOpportunity;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditInvestmentOpportunity extends EditRecord
{
    protected static string $resource = InvestmentOpportunityResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof InvestmentOpportunity, 404);

        return app(InvestmentManager::class)->update(auth()->user(), $record, $data);
    }
}
