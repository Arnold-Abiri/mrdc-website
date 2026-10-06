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
                    if ($value === null || trim($value) === '') {
                        $model->translations()->where($attributes)->delete();

                        continue;
                    }
                    $model->translations()->updateOrCreate($attributes, ['value' => $value, 'updated_by' => $actor->id]);
                }
            }
            app(AuditWriter::class)->record($actor, 'translations.saved', $model, ['locales' => array_keys($data)]);
        });
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
