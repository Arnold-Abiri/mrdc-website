<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Page;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TranslationCompletenessWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        $models = [
            EditorialItem::class,
            Service::class,
            Document::class,
            Page::class,
            Tender::class,
            Vacancy::class,
        ];

        $totalEntities = 0;
        $shonaComplete = 0;
        $ndebeleComplete = 0;

        foreach ($models as $modelClass) {
            $records = $modelClass::query()->with('translations')->whereIn('status', ['published', 'draft'])->get();
            foreach ($records as $record) {
                $totalEntities++;
                $completeness = $record->translationCompleteness();
                if ($completeness['sn']['status'] === 'complete') {
                    $shonaComplete++;
                }
                if ($completeness['nd']['status'] === 'complete') {
                    $ndebeleComplete++;
                }
            }
        }

        $shonaPercent = $totalEntities > 0 ? (int) round(($shonaComplete / $totalEntities) * 100) : 100;
        $ndebelePercent = $totalEntities > 0 ? (int) round(($ndebeleComplete / $totalEntities) * 100) : 100;

        return [
            Stat::make('Translatable Records', $totalEntities)
                ->description('Published & draft content items')
                ->color('primary'),
            Stat::make('Shona Readiness', "{$shonaPercent}%")
                ->description("{$shonaComplete} of {$totalEntities} fully translated")
                ->color($shonaPercent >= 80 ? 'success' : ($shonaPercent >= 40 ? 'warning' : 'danger')),
            Stat::make('Ndebele Readiness', "{$ndebelePercent}%")
                ->description("{$ndebeleComplete} of {$totalEntities} fully translated")
                ->color($ndebelePercent >= 80 ? 'success' : ($ndebelePercent >= 40 ? 'warning' : 'danger')),
        ];
    }
}
