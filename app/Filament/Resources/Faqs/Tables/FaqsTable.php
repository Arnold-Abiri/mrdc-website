<?php

namespace App\Filament\Resources\Faqs\Tables;

use App\Domain\Cms\FaqManager;
use App\Models\Faq;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No FAQs yet')
            ->emptyStateDescription('Create common questions and answers for ratepayers and citizens.')
            ->emptyStateIcon(Heroicon::OutlinedQuestionMarkCircle)
            ->columns([
                TextColumn::make('question')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Faq::CATEGORIES[$state] ?? $state)
                    ->colors([
                        'primary' => 'billing',
                        'success' => 'licences',
                        'warning' => 'land_housing',
                        'info' => 'governance',
                        'danger' => 'health_water',
                        'gray' => 'general',
                    ])
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('All / General')
                    ->toggleable(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(Faq::CATEGORIES),
                SelectFilter::make('is_published')
                    ->label('Publication')
                    ->options([
                        1 => 'Published',
                        0 => 'Draft / Hidden',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->using(fn (Faq $record) => app(FaqManager::class)->delete(auth()->user(), $record)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
