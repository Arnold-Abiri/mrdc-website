<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface TranslatableContent
{
    /** @return list<string> */
    public static function translatableFields(): array;

    public function translations(): MorphMany;

    public function translated(string $field, ?string $locale = null): mixed;

    /**
     * @param  list<string>  $fields
     * @return array<string, mixed>
     */
    public function toLocalizedArray(array $fields, ?string $locale = null): array;

    /**
     * @return array{sn: array{status: string, translated: int, required: int}, nd: array{status: string, translated: int, required: int}}
     */
    public function translationCompleteness(): array;
}
