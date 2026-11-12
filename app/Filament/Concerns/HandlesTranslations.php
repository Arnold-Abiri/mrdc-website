<?php

namespace App\Filament\Concerns;

use App\Domain\Content\TranslatableContent;
use App\Domain\Content\TranslationManager;
use App\Models\EditorialItem;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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
        // Translation fields are dehydrated(false) so getState() drops them;
        // read the raw form state instead. TranslationManager validates.
        $rawState = $this->form->getRawState();
        $rawState = $rawState instanceof Arrayable ? $rawState->toArray() : (array) $rawState;
        foreach ($rawState as $key => $value) {
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

    protected function makeAiTranslateCreateAction(): Action
    {
        return Action::make('ai_translate')
            ->label('AI Translate')->icon('heroicon-o-sparkles')
            ->authorize(fn (): bool => static::getResource()::canCreate())
            ->form([
                Select::make('locale')->label('Target Language')
                    ->options(['sn' => 'ChiShona', 'nd' => 'isiNdebele'])->default('sn')->required(),
            ])
            ->action(function (array $data): void {
                $model = static::getResource()::getModel();
                $fields = [];
                foreach ($model::translatableFields() as $field) {
                    $value = $this->data[$field] ?? null;
                    if (is_array($value)) {
                        $value = TranslationFields::englishToLines(array_values(array_map(
                            fn (mixed $line): mixed => is_array($line) && array_key_exists('value', $line) ? $line['value'] : $line,
                            $value,
                        )));
                    }
                    if (is_string($value) && trim($value) !== '') {
                        $fields[$field] = $value;
                    }
                }
                if ($fields === []) {
                    Notification::make()->title('No Content to Translate')
                        ->body('Enter the English text first.')->warning()->send();

                    return;
                }
                $result = app(GeminiStaffService::class)->translateFields(class_basename($model), $fields, $data['locale']);
                if (! ($result['success'] ?? false)) {
                    Notification::make()->title('AI Translation Failed')
                        ->body($result['error'] ?? 'Unable to generate translations.')->danger()->send();

                    return;
                }
                foreach (array_keys($fields) as $field) {
                    $translation = $result['translations'][$field] ?? null;
                    if (is_string($translation) && trim($translation) !== '') {
                        $this->data["content_translations_{$data['locale']}_{$field}"] = $translation;
                    }
                }
                Notification::make()->title('Translations ready')
                    ->body('Review the translations below, then press Create to save.')
                    ->success()->send();
            });
    }

    /**
     * Create a standard Filament Action for AI translation of any translatable record.
     */
    public static function makeAiTranslateAction(): Action
    {
        return Action::make('ai_translate')
            ->label('AI Translate')
            ->icon('heroicon-o-sparkles')
            ->visible(fn (mixed $livewire, ?Model $record): bool => ($record ?? ($livewire instanceof EditRecord ? $livewire->getRecord() : null)) !== null)
            ->authorize(fn (mixed $livewire, ?Model $record): bool => Gate::allows('update', $record ?? ($livewire instanceof EditRecord ? $livewire->getRecord() : null)))
            ->form([
                Select::make('locale')
                    ->label('Target Language')
                    ->options([
                        'sn' => 'ChiShona',
                        'nd' => 'isiNdebele',
                    ])
                    ->default('sn')
                    ->required(),
            ])
            ->action(function (array $data, mixed $livewire, ?Model $record = null): void {
                $targetRecord = $record ?? ($livewire instanceof EditRecord ? $livewire->getRecord() : null);
                if (! $targetRecord instanceof TranslatableContent || ! $targetRecord instanceof Model) {
                    return;
                }
                $record = $targetRecord;

                $fields = $record::translatableFields();
                $fieldsWithValues = [];
                foreach ($fields as $field) {
                    $val = $record->getAttribute($field);
                    if (is_array($val)) {
                        $val = TranslationFields::englishToLines($val);
                    }
                    if (is_string($val) && trim($val) !== '') {
                        $fieldsWithValues[$field] = $val;
                    }
                }

                if ($fieldsWithValues === []) {
                    Notification::make()
                        ->title('No Content to Translate')
                        ->body('Please ensure the English fields have content before running AI translation.')
                        ->warning()
                        ->send();

                    return;
                }

                $service = app(GeminiStaffService::class);

                if ($record instanceof EditorialItem) {
                    $result = $service->translateEditorial(
                        (string) $record->title,
                        (string) ($record->summary ?? ''),
                        (string) $record->body,
                        $record->seo_title,
                        $record->meta_description,
                        $data['locale']
                    );
                    $translations = [
                        'title' => $result['title'] ?? null,
                        'summary' => $result['summary'] ?? null,
                        'body' => $result['body'] ?? null,
                        'seo_title' => $result['seo_title'] ?? null,
                        'meta_description' => $result['meta_description'] ?? null,
                    ];
                } else {
                    $modelLabel = class_basename($record);
                    $result = $service->translateFields($modelLabel, $fieldsWithValues, $data['locale']);
                    $translations = $result['translations'] ?? [];
                }

                if (! ($result['success'] ?? false)) {
                    Notification::make()
                        ->title('AI Translation Failed')
                        ->body($result['error'] ?? 'Unknown error occurred.')
                        ->danger()
                        ->send();

                    return;
                }

                $input = [$data['locale'] => $translations];

                app(TranslationManager::class)->saveTranslations(auth()->user(), $record, $input);

                $langName = $data['locale'] === 'nd' ? 'isiNdebele' : 'ChiShona';
                Notification::make()
                    ->title("Saved {$langName} Translations")
                    ->body("Gemini successfully generated and saved {$langName} translations for ".count($translations).' field(s).')
                    ->success()
                    ->send();

                if ($livewire instanceof EditRecord) {
                    $livewire->record = $record->refresh();
                    $livewire->fillForm();
                }
            });
    }
}
