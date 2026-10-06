<?php

namespace App\Filament\Concerns;

use App\Domain\Content\TranslatableContent;
use App\Models\ContentTranslation;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class TranslationFields
{
    /**
     * Shona/Ndebele textareas for an entity's translatable fields.
     * Values are display-only for the form lifecycle (dehydrated(false))
     * and persisted by the HandlesTranslations page trait.
     *
     * @param  list<string>  $fields
     */
    public static function make(array $fields): Section
    {
        $components = [];
        foreach (['sn' => 'Shona', 'nd' => 'Ndebele'] as $locale => $language) {
            foreach ($fields as $field) {
                $components[] = Textarea::make("content_translations_{$locale}_{$field}")
                    ->label(ucwords(str_replace('_', ' ', (string) $field))." ({$language})")
                    ->rows(3)
                    ->maxLength(50000)
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Textarea $component, mixed $record) use ($locale, $field): void {
                        if ($record instanceof TranslatableContent) {
                            $row = $record->translations()->where('locale', $locale)->where('field', $field)->first();
                            if ($row instanceof ContentTranslation) {
                                $component->state($row->value);
                            }
                        }
                    })
                    ->helperText('Leave empty to fall back to English. Only council-approved wording.');
            }
        }

        return Section::make('Translations')->description('Shona and Ndebele public wording. Missing entries fall back to English.')->schema($components)->collapsible()->collapsed();
    }
}
