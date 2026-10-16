<?php

namespace App\Filament\Resources\Statistics\Pages;

use App\Filament\Resources\Statistics\DistrictStatisticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDistrictStatistics extends ListRecords
{
    protected static string $resource = DistrictStatisticResource::class;

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
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_active', true))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('is_active', true)->count())
                ->badgeColor('success'),
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_active', false))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('is_active', false)->count())
                ->badgeColor('gray'),
        ];
    }
}
