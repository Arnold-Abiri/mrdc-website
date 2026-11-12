<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use App\Filament\Concerns\TranslationFields;
use App\Models\BankAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account details')
                    ->schema([
                        Select::make('payment_type')
                            ->label('Payment Type')
                            ->options(['bank' => 'Bank Account', 'mobile' => 'Mobile Money'])
                            ->default('bank')
                            ->required()
                            ->live(),
                        TextInput::make('bank_name')
                            ->label(fn (Get $get): string => $get('payment_type') === 'mobile' ? 'Provider Name' : 'Bank Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('account_name')
                            ->label(fn (Get $get): string => $get('payment_type') === 'mobile' ? 'Payment Title' : 'Account Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('account_number')
                            ->label(fn (Get $get): string => $get('payment_type') === 'mobile' ? 'Biller / Merchant Code' : 'Account Number')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('branch_name')
                            ->label('Branch Name')
                            ->visible(fn (Get $get): bool => $get('payment_type') !== 'mobile')
                            ->required(fn (Get $get): bool => $get('payment_type') !== 'mobile')
                            ->maxLength(255),
                        TextInput::make('branch_code')
                            ->label('Branch Code')
                            ->visible(fn (Get $get): bool => $get('payment_type') !== 'mobile')
                            ->maxLength(50),
                        TextInput::make('code_label')
                            ->label('Code Label')
                            ->placeholder('Biller Code or Merchant Code')
                            ->visible(fn (Get $get): bool => $get('payment_type') === 'mobile')
                            ->required(fn (Get $get): bool => $get('payment_type') === 'mobile')
                            ->maxLength(255),
                        Textarea::make('instructions')
                            ->label('Payment Instructions')
                            ->helperText('Enter one step per line. Update any code mentioned in the steps when changing the payment code.')
                            ->rows(6)
                            ->visible(fn (Get $get): bool => $get('payment_type') === 'mobile')
                            ->required(fn (Get $get): bool => $get('payment_type') === 'mobile')
                            ->columnSpanFull(),
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
                    ]),
                TranslationFields::make(BankAccount::translatableFields()),
                Section::make('Visibility')
                    ->schema([
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
                    ]),
            ]);
    }
}
