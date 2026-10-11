<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('account_name')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('account_number')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Account number copied')
                    ->fontFamily('mono')
                    ->weight('bold'),
                TextColumn::make('branch_name')
                    ->searchable(),
                TextColumn::make('currency')
                    ->badge()
                    ->colors([
                        'warning' => 'ZiG',
                        'success' => 'USD / Nostro',
                        'info' => 'Multi-Currency',
                    ]),
                TextColumn::make('category')
                    ->badge(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'published',
                        'warning' => 'draft',
                        'danger' => 'archived',
                    ]),
                TextColumn::make('display_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
