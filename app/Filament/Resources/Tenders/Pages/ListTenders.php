<?php

namespace App\Filament\Resources\Tenders\Pages;

use App\Filament\Resources\Tenders\TenderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTenders extends ListRecords
{
    protected static string $resource = TenderResource::class;

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
            'open' => Tab::make('Open')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('lifecycle_status', 'open'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('lifecycle_status', 'open')->count())
                ->badgeColor('success'),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('lifecycle_status', 'closed'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('lifecycle_status', 'closed')->count())
                ->badgeColor('gray'),
            'awarded' => Tab::make('Awarded')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('lifecycle_status', 'awarded'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('lifecycle_status', 'awarded')->count())
                ->badgeColor('info'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'draft'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'draft')->count())
                ->badgeColor('gray'),
        ];
    }
}
