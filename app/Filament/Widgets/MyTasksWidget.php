<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Filament\Resources\Tenders\TenderResource;
use App\Filament\Resources\Vacancies\VacancyResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;

class MyTasksWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        $user = auth()->user();

        $assignedEnquiries = $user && Schema::hasColumn('enquiries', 'assigned_to')
            ? EnquiryResource::getEloquentQuery()->where('assigned_to', $user->getKey())->whereNotIn('status', ['resolved', 'closed'])->count()
            : 0;

        $pendingApprovals = EditorialItemResource::canViewAny()
            ? EditorialItemResource::getEloquentQuery()->where(fn ($q) => $q->where('status', 'draft')->orWhere('verification_status', 'pending'))->count()
            : 0;

        $closingSoon = 0;
        if (TenderResource::canViewAny()) {
            $closingSoon += TenderResource::getEloquentQuery()->whereDate('closes_at', '>=', today())->whereDate('closes_at', '<=', today()->addDays(14))->count();
        }
        if (VacancyResource::canViewAny()) {
            $closingSoon += VacancyResource::getEloquentQuery()->whereDate('closes_at', '>=', today())->whereDate('closes_at', '<=', today()->addDays(14))->count();
        }

        return [
            Stat::make('Assigned to me', $assignedEnquiries)
                ->description('Open enquiries assigned to you')
                ->url(EnquiryResource::getUrl('index')),
            Stat::make('Pending approval', $pendingApprovals)
                ->description('Drafts and items under review')
                ->url(EditorialItemResource::getUrl('index')),
            Stat::make('Closing in 14 days', $closingSoon)
                ->description('Tenders and vacancies near deadline')
                ->url(TenderResource::getUrl('index')),
        ];
    }
}
