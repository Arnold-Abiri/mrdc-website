<?php

namespace App\Filament\Pages;

use App\Domain\Cms\TenderManager;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read array{total: int, open: int, closed: int, awarded: int, cancelled: int, overdue_close: int, published: int, drafts: int} $summary
 */
class TenderCompliance extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $navigationLabel = 'Tender compliance';
    protected static \UnitEnum|string|null $navigationGroup = 'Services & Development';
    protected static ?int $navigationSort = 7;

    protected static ?string $title = 'Tender compliance';

    protected string $view = 'filament.pages.tender-compliance';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('tenders.view') ?? false;
    }

    /** @return array{total: int, open: int, closed: int, awarded: int, cancelled: int, overdue_close: int, published: int, drafts: int} */
    public function getSummaryProperty(): array
    {
        return app(TenderManager::class)->complianceSummary();
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return ['summary' => $this->summary];
    }
}
