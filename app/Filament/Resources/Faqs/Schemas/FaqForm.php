<?php

namespace App\Filament\Resources\Faqs\Schemas;

use App\Filament\Concerns\TranslationFields;
use App\Models\Faq;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FAQ Details')
                    ->description('Constituent question, detailed answer and service category.')
                    ->schema([
                        TextInput::make('question')
                            ->label('Question (English)')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->label('Answer (English)')
                            ->required()
                            ->rows(5)
                            ->maxLength(50000)
                            ->helperText('Clear, authoritative answer for ratepayers and citizens.')
                            ->columnSpanFull(),
                        Select::make('category')
                            ->options(Faq::CATEGORIES)
                            ->required()
                            ->default('general')
                            ->searchable(),
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->label('Council Department')
                            ->searchable()
                            ->preload()
                            ->placeholder('All / General Council'),
                        TextInput::make('display_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Lower numbers appear first within category.'),
                        Toggle::make('is_published')
                            ->label('Published on Public Portal')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Featured FAQ')
                            ->helperText('Highlights this question at the top of the FAQ hub.')
                            ->default(false),
                    ])
                    ->columns(2),
                TranslationFields::make(Faq::translatableFields()),
            ]);
    }
}
