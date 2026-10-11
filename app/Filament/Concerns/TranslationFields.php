<?php

namespace App\Filament\Concerns;

use App\Domain\Content\TranslatableContent;
use App\Models\ContentTranslation;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Model;

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
                        if ($record instanceof TranslatableContent && $record instanceof Model) {
                            $row = $record->translations()->where('locale', $locale)->where('field', $field)->first();
                            $english = $record->getAttribute($field);
                            if ($row instanceof ContentTranslation && trim($row->value) !== '') {
                                $component->state(self::translationToLines($english, $row->value));
                            } elseif (is_array($english)) {
                                $component->state(self::englishToLines($english));
                            }
                        }
                    })
                    ->helperText('Leave empty to fall back to English. Only council-approved wording. For lists use one item per line; for page text use one paragraph per block, in order.');
            }
        }

        return Section::make('Translations')->description('Shona and Ndebele public wording. Missing entries fall back to English.')->schema($components)->collapsible()->collapsed();
    }

    /**
     * Render a stored translation (or the English source) as editable lines.
     */
    private static function translationToLines(mixed $english, string $stored): string
    {
        if (! is_array($english)) {
            return $stored;
        }
        $decoded = json_decode($stored, true);
        $lines = is_array($decoded) ? $decoded : [];
        if ($lines === []) {
            return self::englishToLines($english);
        }
        $separator = self::isBlocks($english) ? "\n\n" : "\n";

        return implode($separator, array_map(fn ($line): string => trim((string) $line), $lines));
    }

    /**
     * Render the English source of a list field as editable lines.
     *
     * @param  array<int, mixed>  $english
     */
    private static function englishToLines(array $english): string
    {
        if (self::isBlocks($english)) {
            $texts = [];
            foreach ($english as $block) {
                if (is_array($block) && isset($block['text']) && trim((string) $block['text']) !== '') {
                    $texts[] = trim((string) $block['text']);
                }
            }

            return implode("\n\n", $texts);
        }

        return implode("\n", array_map(fn ($line): string => trim((string) $line), $english));
    }

    /**
     * @param  array<int, mixed>  $english
     */
    private static function isBlocks(array $english): bool
    {
        return isset($english[0]) && is_array($english[0]) && array_key_exists('text', $english[0]);
    }
}
