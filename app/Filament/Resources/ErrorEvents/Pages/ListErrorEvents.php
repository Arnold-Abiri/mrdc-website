<?php

namespace App\Filament\Resources\ErrorEvents\Pages;

use App\Filament\Resources\ErrorEvents\ErrorEventResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListErrorEvents extends ListRecords
{
    protected static string $resource = ErrorEventResource::class;

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->count())
                ->badgeColor('gray'),
            'open' => Tab::make('Open')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('resolved', false))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('resolved', false)->count())
                ->badgeColor('warning'),
            'resolved' => Tab::make('Resolved')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('resolved', true))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('resolved', true)->count())
                ->badgeColor('success'),
        ];
    }
}
