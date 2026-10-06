<?php

namespace App\Domain\Content;

use App\Models\ContentTranslation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Polymorphic content translations with English fallback.
 *
 * Only Shona (sn) and Ndebele (nd) rows are stored; English always comes
 * from the model's own attributes and is the authoritative fallback.
 */
trait HasTranslations
{
    /** @return list<string> */
    public static function translatableFields(): array
    {
        return static::TRANSLATABLE_FIELDS ?? [];
    }

    /** @return MorphMany<ContentTranslation, $this> */
    public function translations(): MorphMany
    {
        return $this->morphMany(ContentTranslation::class, 'translatable');
    }

    public function translated(string $field, ?string $locale = null): mixed
    {
        $locale ??= app()->getLocale();
        $english = $this->getAttribute($field);
        if (! in_array($locale, ContentTranslation::LOCALES, true)) {
            return $english;
        }
        $row = $this->relationLoaded('translations')
            ? $this->translations->first(fn (ContentTranslation $translation): bool => $translation->locale === $locale && $translation->field === $field)
            : $this->translations()->where('locale', $locale)->where('field', $field)->first();
        if (! $row instanceof ContentTranslation || trim($row->value) === '') {
            return $english;
        }
        if (is_array($english)) {
            $decoded = json_decode($row->value, true);

            return is_array($decoded) ? $decoded : $english;
        }

        return $row->value;
    }

    /**
     * @param  list<string>  $fields
     * @return array<string, mixed>
     */
    public function toLocalizedArray(array $fields, ?string $locale = null): array
    {
        $base = $this->only($fields);
        foreach ($fields as $field) {
            if (in_array($field, static::translatableFields(), true)) {
                $base[$field] = $this->translated($field, $locale);
            }
        }

        return $base;
    }

    /**
     * @return array{sn: array{status: string, translated: int, required: int}, nd: array{status: string, translated: int, required: int}}
     */
    public function translationCompleteness(): array
    {
        $fields = static::translatableFields();
        $byLocale = $this->relationLoaded('translations') ? $this->translations : $this->translations()->get();
        $result = [];
        foreach (ContentTranslation::LOCALES as $locale) {
            $done = 0;
            foreach ($fields as $field) {
                $row = $byLocale->first(fn (ContentTranslation $translation): bool => $translation->locale === $locale && $translation->field === $field);
                if ($row instanceof ContentTranslation && trim($row->value) !== '') {
                    $done++;
                }
            }
            $total = count($fields);
            $result[$locale] = ['status' => $total === 0 || $done === 0 ? 'missing' : ($done >= $total ? 'complete' : 'partial'), 'translated' => $done, 'required' => $total];
        }

        return $result;
    }
}
