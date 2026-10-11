<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bank_name')
                    ->label('Bank Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_name')
                    ->label('Account Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_number')
                    ->label('Account Number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('branch_name')
                    ->label('Branch Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('branch_code')
                    ->label('Branch Code')
                    ->maxLength(50),
                Select::make('currency')
                    ->options([
                        'ZiG' => 'ZiG',
                        'USD / Nostro' => 'USD / Nostro',
                        'Multi-Currency' => 'Multi-Currency',
                    ])
                    ->required()
                    ->default('ZiG'),
                Select::make('category')
                    ->options([
                        'rates' => 'Rates & Water',
                        'stands' => 'Housing & Stands',
                        'licenses' => 'Business & Licences',
                        'general' => 'General Revenue',
                    ])
                    ->required()
                    ->default('rates'),
                Textarea::make('purpose')
                    ->label('Account Purpose & Scope')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('display_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Select::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('published'),
            ]);
    }
}
