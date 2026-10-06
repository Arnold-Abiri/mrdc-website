<?php

namespace App\Filament\Concerns;

use App\Domain\Content\TranslatableContent;
use App\Domain\Content\TranslationManager;

trait HandlesTranslations
{
    protected function afterCreate(): void
    {
        $this->persistContentTranslations();
    }

    protected function afterSave(): void
    {
        $this->persistContentTranslations();
    }

    private function persistContentTranslations(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof TranslatableContent) {
            return;
        }
        $input = ['sn' => [], 'nd' => []];
        foreach ($this->form->getState() as $key => $value) {
            if (! str_starts_with($key, 'content_translations_')) {
                continue;
            }
            $rest = substr($key, strlen('content_translations_'));
            $locale = str_starts_with($rest, 'sn_') ? 'sn' : (str_starts_with($rest, 'nd_') ? 'nd' : null);
            if ($locale === null) {
                continue;
            }
            $input[$locale][substr($rest, 3)] = is_string($value) ? $value : null;
        }
        $input = array_filter($input);
        if ($input === []) {
            return;
        }
        app(TranslationManager::class)->saveTranslations(auth()->user(), $record, $input);
    }
}
