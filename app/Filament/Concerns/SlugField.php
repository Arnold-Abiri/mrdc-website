<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class SlugField
{
    /**
     * Create a standard, clean slug field for Filament resources.
     *
     * - Auto-generates from the title/name attribute on creation.
     * - Preserves the existing slug when editing to prevent broken URLs.
     * - Tucked inside a subtle, collapsed "Web Address (URL Permalink)" section.
     */
    public static function make(
        string $sourceField = 'title',
        string $slugField = 'slug',
        int $maxLength = 160
    ): Section {
        return Section::make('Web Address (URL Permalink)')
            ->description('The public website address for this item.')
            ->schema([
                TextInput::make($slugField)
                    ->label('URL Slug')
                    ->required()
                    ->maxLength($maxLength)
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from the '.$sourceField.'. Only change if you specifically need a custom URL.')
                    ->prefix('/.../'),
            ])
            ->collapsible()
            ->collapsed();
    }

    /**
     * Helper to wire up live slug generation on the source title/name input.
     *
     * Example:
     *   TextInput::make('title')
     *       ->required()
     *       ->live(onBlur: true)
     *       ->afterStateUpdated(SlugField::autoFillHook());
     */
    public static function autoFillHook(string $slugField = 'slug', int $maxLength = 160): \Closure
    {
        return function (?string $state, callable $set, ?string $operation = null) use ($slugField, $maxLength): void {
            if ($operation !== 'create') {
                return;
            }

            $slug = Str::slug((string) $state);
            $set($slugField, $slug === '' ? '' : substr($slug, 0, $maxLength));
        };
    }
}
