<?php

namespace App\Filament\Resources\Enquiries\Pages;

use App\Filament\Resources\Enquiries\EnquiryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEnquiries extends ListRecords
{
    protected static string $resource = EnquiryResource::class;

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->count())
                ->badgeColor('gray'),
            'new' => Tab::make('New')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'new'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'new')->count())
                ->badgeColor('warning'),
            'in_progress' => Tab::make('In progress')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'in_progress'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'in_progress')->count())
                ->badgeColor('info'),
            'resolved' => Tab::make('Resolved')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'resolved'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'resolved')->count())
                ->badgeColor('success'),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'closed'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'closed')->count())
                ->badgeColor('gray'),
        ];
    }
}
