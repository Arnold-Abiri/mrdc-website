<?php

namespace App\Filament\Pages;

use App\Domain\Content\TranslationManager;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\InvestmentOpportunity;
use App\Models\Page;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use BackedEnum;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read array<string, array{label: string, total: int, sn_complete: int, sn_partial: int, nd_complete: int, nd_partial: int}> $report
 */
class TranslationStatus extends FilamentPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Translation status';
    protected static \UnitEnum|string|null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Translation status';

    protected string $view = 'filament.pages.translation-status';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('pages.view');
    }

    /** @return array<string, array{label: string, total: int, sn_complete: int, sn_partial: int, nd_complete: int, nd_partial: int}> */
    public function getReportProperty(): array
    {
        $manager = app(TranslationManager::class);
        $entities = [
            'Pages' => Page::class,
            'Services' => Service::class,
            'News and notices' => EditorialItem::class,
            'Departments' => Department::class,
            'Projects' => CouncilProject::class,
            'Investment' => InvestmentOpportunity::class,
            'Tenders' => Tender::class,
            'Vacancies' => Vacancy::class,
            'Documents' => Document::class,
            'Meetings' => CouncilMeeting::class,
        ];
        $report = [];
        foreach ($entities as $label => $class) {
            $report[$label] = ['label' => $label, ...$manager->completenessFor($class)];
        }

        return $report;
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return ['report' => $this->report];
    }
}
