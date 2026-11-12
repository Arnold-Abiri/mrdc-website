<?php

namespace App\Filament\Resources\BankAccounts\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BankAccounts\BankAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankAccount extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            DeleteAction::make(),
        ];
    }
}
