<?php

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BankAccounts\BankAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankAccount extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [$this->makeAiTranslateCreateAction()];
    }
}
