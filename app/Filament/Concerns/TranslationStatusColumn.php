<?php

namespace App\Filament\Concerns;

use App\Domain\Content\TranslatableContent;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class TranslationStatusColumn
{
    public static function make(string $name = 'translations_status'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Translations')
            ->badge()
            ->state(function (Model $record): array {
                if (! $record instanceof TranslatableContent) {
                    return [];
                }

                $completeness = $record->translationCompleteness();
                $sn = $completeness['sn']['status'] ?? 'missing';
                $nd = $completeness['nd']['status'] ?? 'missing';

                return [
                    "SN: {$sn}",
                    "ND: {$nd}",
                ];
            })
            ->color(fn (string $state): string => match (true) {
                str_contains($state, 'complete') => 'success',
                str_contains($state, 'partial') => 'warning',
                default => 'gray',
            })
            ->toggleable();
    }
}
