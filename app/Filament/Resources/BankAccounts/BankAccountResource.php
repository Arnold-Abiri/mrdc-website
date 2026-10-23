<?php

namespace App\Filament\Resources\BankAccounts;

use App\Domain\Identity\DataScopeAuthorizer;
use App\Filament\Clusters\Services\ServicesCluster;
use App\Filament\Resources\BankAccounts\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccount;
use App\Filament\Resources\BankAccounts\Pages\ListBankAccounts;
use App\Filament\Resources\BankAccounts\Schemas\BankAccountForm;
use App\Filament\Resources\BankAccounts\Tables\BankAccountsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BankAccountResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        return app(DataScopeAuthorizer::class)->applyGlobalOnly(parent::getEloquentQuery(), auth()->user(), 'bank_accounts.view');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $cluster = ServicesCluster::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Bank & Payment Accounts';

    protected static ?string $modelLabel = 'bank account';

    public static function form(Schema $schema): Schema
    {
        return BankAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankAccounts::route('/'),
            'create' => CreateBankAccount::route('/create'),
            'edit' => EditBankAccount::route('/{record}/edit'),
        ];
    }
}
