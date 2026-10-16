<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

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
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_published', true))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('is_published', true)->count())
                ->badgeColor('success'),
            'unpublished' => Tab::make('Unpublished')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_published', false))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('is_published', false)->count())
                ->badgeColor('gray'),
        ];
    }
}
