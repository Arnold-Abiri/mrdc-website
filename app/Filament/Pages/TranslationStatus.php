<?php

namespace App\Filament\Pages;

use App\Domain\Content\TranslationManager;
use App\Filament\Resources\Departments\DepartmentResource;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\Investment\InvestmentOpportunityResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Meetings\CouncilMeetingResource;
use App\Filament\Resources\Officials\OfficialResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Projects\CouncilProjectResource;
use App\Filament\Resources\ProjectUpdates\ProjectUpdateResource;
use App\Filament\Resources\PublicContacts\PublicContactResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Slides\HomepageSlideResource;
use App\Filament\Resources\Statistics\DistrictStatisticResource;
use App\Filament\Resources\Tenders\TenderResource;
use App\Filament\Resources\Vacancies\VacancyResource;
use App\Filament\Resources\Wards\WardResource;
use App\Models\CouncilMeeting;
use App\Models\CouncilProject;
use App\Models\Department;
use App\Models\DistrictStatistic;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomepageSlide;
use App\Models\InvestmentOpportunity;
use App\Models\Media;
use App\Models\Official;
use App\Models\Page;
use App\Models\ProjectUpdate;
use App\Models\PublicContact;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use App\Models\Ward;
use BackedEnum;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;

class TranslationStatus extends FilamentPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Translation status';

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Translation status';

    protected string $view = 'filament.pages.translation-status';

    /** @return array<string, array{model: class-string, resource: class-string}> */
    public static function entities(): array
    {
        return [
            'Pages' => ['model' => Page::class, 'resource' => PageResource::class],
            'Services' => ['model' => Service::class, 'resource' => ServiceResource::class],
            'News and notices' => ['model' => EditorialItem::class, 'resource' => EditorialItemResource::class],
            'Departments' => ['model' => Department::class, 'resource' => DepartmentResource::class],
            'Projects' => ['model' => CouncilProject::class, 'resource' => CouncilProjectResource::class],
            'Investment' => ['model' => InvestmentOpportunity::class, 'resource' => InvestmentOpportunityResource::class],
            'Tenders' => ['model' => Tender::class, 'resource' => TenderResource::class],
            'Vacancies' => ['model' => Vacancy::class, 'resource' => VacancyResource::class],
            'Documents' => ['model' => Document::class, 'resource' => DocumentResource::class],
            'Meetings' => ['model' => CouncilMeeting::class, 'resource' => CouncilMeetingResource::class],
            'Galleries' => ['model' => Gallery::class, 'resource' => GalleryResource::class],
            'Wards' => ['model' => Ward::class, 'resource' => WardResource::class],
            'Officials' => ['model' => Official::class, 'resource' => OfficialResource::class],
            'Homepage slides' => ['model' => HomepageSlide::class, 'resource' => HomepageSlideResource::class],
            'District statistics' => ['model' => DistrictStatistic::class, 'resource' => DistrictStatisticResource::class],
            'Media captions' => ['model' => Media::class, 'resource' => MediaResource::class],
            'Project updates' => ['model' => ProjectUpdate::class, 'resource' => ProjectUpdateResource::class],
            'Public contacts' => ['model' => PublicContact::class, 'resource' => PublicContactResource::class],
            'FAQs' => ['model' => Faq::class, 'resource' => FaqResource::class],
        ];
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('pages.view');
    }

    /**
     * Raw per-entity coverage. Cached as plain arrays (never cache
     * Collection objects) and shared with the console translation command.
     *
     * @return array<string, array{label: string, total: int, sn_complete: int, sn_partial: int, nd_complete: int, nd_partial: int}>
     */
    public function getReportProperty(): array
    {
        return Cache::remember('translation_status_report', 300, function (): array {
            $manager = app(TranslationManager::class);
            $report = [];
            foreach (self::entities() as $label => $entity) {
                $report[$label] = ['label' => $label, ...$manager->completenessFor($entity['model'])];
            }

            return $report;
        });
    }

    /**
     * @return list<array{label: string, total: int, sn_complete: int, sn_partial: int, nd_complete: int, nd_partial: int, sn_complete_pct: float, sn_partial_pct: float, nd_complete_pct: float, nd_partial_pct: float, score: float, status: string, url: ?string}>
     */
    public function getRowsProperty(): array
    {
        $rows = [];
        foreach ($this->report as $label => $row) {
            $total = $row['total'];
            $score = $total > 0 ? round((($row['sn_complete'] + $row['nd_complete']) / (2 * $total)) * 100, 1) : 0;
            $resource = self::entities()[$label]['resource'];
            $rows[] = [
                ...$row,
                'sn_complete_pct' => $total > 0 ? round($row['sn_complete'] / $total * 100, 1) : 0,
                'sn_partial_pct' => $total > 0 ? round($row['sn_partial'] / $total * 100, 1) : 0,
                'nd_complete_pct' => $total > 0 ? round($row['nd_complete'] / $total * 100, 1) : 0,
                'nd_partial_pct' => $total > 0 ? round($row['nd_partial'] / $total * 100, 1) : 0,
                'score' => $score,
                'status' => $total === 0
                    ? 'empty'
                    : ($row['sn_complete'] === $total && $row['nd_complete'] === $total
                        ? 'complete'
                        : (($row['sn_complete'] + $row['sn_partial'] + $row['nd_complete'] + $row['nd_partial']) === 0 ? 'untouched' : 'in-progress')),
                'url' => $resource::canViewAny() ? $resource::getUrl() : null,
            ];
        }
        usort($rows, fn (array $a, array $b): int => $b['total'] <=> $a['total']);

        return $rows;
    }

    /** @return array{records: int, sn_complete: int, nd_complete: int, sn_pct: int, nd_pct: int} */
    public function getTotalsProperty(): array
    {
        $totals = ['records' => 0, 'sn_complete' => 0, 'nd_complete' => 0];
        foreach ($this->report as $row) {
            $totals['records'] += $row['total'];
            $totals['sn_complete'] += $row['sn_complete'];
            $totals['nd_complete'] += $row['nd_complete'];
        }

        return [
            ...$totals,
            'sn_pct' => $totals['records'] > 0 ? (int) round($totals['sn_complete'] / $totals['records'] * 100) : 0,
            'nd_pct' => $totals['records'] > 0 ? (int) round($totals['nd_complete'] / $totals['records'] * 100) : 0,
        ];
    }

    /** @return list<array{label: string, score: float, url: ?string}> */
    public function getPriorityRowsProperty(): array
    {
        $rows = array_filter($this->rows, fn (array $row): bool => $row['total'] > 0 && $row['status'] !== 'complete');
        usort($rows, fn (array $a, array $b): int => $a['score'] <=> $b['score']);

        return array_slice(array_map(fn (array $row): array => [
            'label' => $row['label'],
            'score' => $row['score'],
            'url' => $row['url'],
        ], $rows), 0, 4);
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'rows' => $this->rows,
            'totals' => $this->totals,
            'priorityRows' => $this->priorityRows,
        ];
    }
}
