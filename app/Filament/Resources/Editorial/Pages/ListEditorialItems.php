<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Filament\Resources\Editorial\EditorialItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEditorialItems extends ListRecords
{
    protected static string $resource = EditorialItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->count())
                ->badgeColor('gray'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'draft'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'draft')->count())
                ->badgeColor('gray'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'published'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'published')->count())
                ->badgeColor('success'),
        ];
    }
}
