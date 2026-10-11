<?php

namespace App\Domain\Content;

use App\Domain\Identity\AuditWriter;
use App\Models\ContentTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TranslationManager
{
    /**
     * Persist Shona/Ndebele field translations for any translatable record.
     * Empty values remove the stored row (missing state). English attributes
     * are never written here. Requires update permission on the record.
     *
     * @param  array<string, array<string, ?string>>  $input  e.g. ['sn' => ['title' => '...'], 'nd' => [...]]
     */
    public function saveTranslations(User $actor, Model&TranslatableContent $model, array $input): void
    {
        Gate::forUser($actor)->authorize('update', $model);
        $fields = $model::translatableFields();
        abort_if($fields === [], 422, 'Model does not support translations.');
        $data = Validator::make($input, [
            '*' => ['array'],
            '*.*' => ['nullable', 'string', 'max:50000'],
        ])->validate();
        foreach (array_keys($data) as $locale) {
            abort_unless(in_array($locale, ContentTranslation::LOCALES, true), 422, 'Unsupported translation locale.');
            abort_unless(empty(array_diff(array_keys($data[$locale]), $fields)), 422, 'Unknown translatable field.');
        }

        DB::transaction(function () use ($actor, $model, $data): void {
            foreach ($data as $locale => $values) {
                foreach ($values as $field => $value) {
                    $attributes = ['locale' => $locale, 'field' => $field];
                    $stored = self::encodeTranslationValue($model, $field, $value);
                    if ($stored === null) {
                        $model->translations()->where($attributes)->delete();

                        continue;
                    }
                    $model->translations()->updateOrCreate($attributes, ['value' => $stored, 'updated_by' => $actor->id]);
                }
            }
            app(AuditWriter::class)->record($actor, 'translations.saved', $model, ['locales' => array_keys($data)]);
        });
    }

    /**
     * Encode a translator's textarea input for storage.
     *
     * List fields (requirements, steps) use one item per line; page blocks
     * use one paragraph per blank-line-separated block. Both are stored as
     * JSON lists so the public reader can merge them positionally, with
     * English fallback on any mismatch. Returns null when empty so the
     * stored row is removed and English is served.
     */
    private static function encodeTranslationValue(Model&TranslatableContent $model, string $field, ?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $english = $model->getAttribute($field);
        if (! is_array($english) || $english === []) {
            return $value;
        }
        $isBlocks = isset($english[0]) && is_array($english[0]) && array_key_exists('text', $english[0]);
        $parts = $isBlocks ? preg_split('/\R\s*\R/', $value) : preg_split('/\R/', $value);
        $lines = array_values(array_filter(array_map(fn ($line): string => trim((string) $line), $parts === false ? [] : $parts), fn ($line): bool => $line !== ''));

        return $lines === [] ? null : json_encode($lines, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  class-string<Model&TranslatableContent>  $class
     * @return array{total: int, sn_complete: int, sn_partial: int, nd_complete: int, nd_partial: int}
     */
    public function completenessFor(string $class): array
    {
        $summary = ['total' => 0, 'sn_complete' => 0, 'sn_partial' => 0, 'nd_complete' => 0, 'nd_partial' => 0];
        $class::query()->with('translations')->chunk(200, function ($records) use (&$summary): void {
            foreach ($records as $record) {
                $summary['total']++;
                $status = $record->translationCompleteness();
                foreach (['sn', 'nd'] as $locale) {
                    if ($status[$locale]['status'] === 'complete') {
                        $summary[$locale.'_complete']++;
                    } elseif ($status[$locale]['status'] === 'partial') {
                        $summary[$locale.'_partial']++;
                    }
                }
            }
        });

        return $summary;
    }
}
