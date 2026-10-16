<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\CouncilMeetingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCouncilMeetings extends ListRecords
{
    protected static string $resource = CouncilMeetingResource::class;

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
            'scheduled' => Tab::make('Scheduled')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('meeting_status', 'scheduled'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('meeting_status', 'scheduled')->count())
                ->badgeColor('info'),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('meeting_status', 'completed'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('meeting_status', 'completed')->count())
                ->badgeColor('success'),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('meeting_status', 'cancelled'))
                ->badge(fn (): int => static::getResource()::getEloquentQuery()->where('meeting_status', 'cancelled')->count())
                ->badgeColor('danger'),
        ];
    }
}
