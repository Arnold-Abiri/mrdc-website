<?php

namespace App\Filament\Concerns;

use App\Models\CouncilMeeting;
use App\Models\Document;
use App\Models\EditorialItem;
use App\Models\Gallery;
use App\Models\InvestmentOpportunity;
use App\Models\Official;
use App\Models\Page;
use App\Models\Service;
use App\Models\Tender;
use App\Models\Vacancy;
use App\Models\Ward;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PreviewHelper
{
    /**
     * Generate a signed preview URL for any previewable record.
     */
    public static function getSignedPreviewUrl(Model $record, string $locale = 'en'): ?string
    {
        if ($record instanceof Page) {
            return URL::temporarySignedRoute(
                'preview.page',
                now()->addHours(2),
                ['locale' => $locale, 'slug' => $record->slug]
            );
        }

        $type = match (true) {
            $record instanceof Service => 'services',
            $record instanceof EditorialItem => $record->type === 'notice' ? 'notices' : 'news',
            $record instanceof Document => 'documents',
            $record instanceof Ward => 'wards',
            $record instanceof Official => 'officials',
            $record instanceof InvestmentOpportunity => 'investment',
            $record instanceof CouncilMeeting => 'meetings',
            $record instanceof Gallery => 'galleries',
            $record instanceof Tender => 'tenders',
            $record instanceof Vacancy => 'vacancies',
            default => null,
        };

        if ($type === null) {
            return null;
        }

        $param = $record instanceof CouncilMeeting ? $record->id : ($record->slug ?? null);
        if (! $param) {
            return null;
        }

        return URL::temporarySignedRoute(
            'preview.detail',
            now()->addHours(2),
            [
                'locale' => $locale,
                'type' => $type,
                'slug' => (string) $param,
            ]
        );
    }

    /**
     * Get a Filament header/record action for previewing on the public site.
     */
    public static function makePreviewAction(): Action
    {
        return Action::make('previewWebsite')
            ->label('Preview on Website')
            ->icon(Heroicon::OutlinedEye)
            ->color('gray')
            ->visible(fn (?Model $record): bool => $record !== null && self::getSignedPreviewUrl($record) !== null)
            ->url(fn (Model $record): ?string => self::getSignedPreviewUrl($record))
            ->openUrlInNewTab();
    }
}
