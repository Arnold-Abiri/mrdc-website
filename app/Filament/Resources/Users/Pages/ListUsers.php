<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /** @return array<string, Tab> */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->count())
                ->badgeColor('gray'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'active'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'active')->count())
                ->badgeColor('success'),
            'disabled' => Tab::make('Disabled')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'disabled'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('status', 'disabled')->count())
                ->badgeColor('gray'),
        ];
    }
}
