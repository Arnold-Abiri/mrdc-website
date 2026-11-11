<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Domain\Cms\PageManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\TranslationFields;
use App\Filament\Resources\Pages\PageResource;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePage extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = PageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(PageManager::class)->create(auth()->user(), $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->color('info')
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
                ->action(function (array $data): void {
                    $state = $this->form->getRawState();
                    $state = is_array($state) ? $state : [];
                    $fields = [];
                    foreach (['title', 'summary', 'seo_title', 'meta_description'] as $field) {
                        $value = $state[$field] ?? null;
                        if (is_string($value) && trim($value) !== '') {
                            $fields[$field] = $value;
                        }
                    }
                    $blocks = $state['blocks'] ?? null;
                    if (is_array($blocks) && $blocks !== []) {
                        $fields['blocks'] = TranslationFields::englishToLines(array_values($blocks));
                    }
                    $fields = array_filter($fields, fn ($value): bool => trim((string) $value) !== '');

                    if ($fields === []) {
                        Notification::make()
                            ->title('Nothing to Translate')
                            ->body('Fill in the English title, summary, or content first.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $result = app(GeminiStaffService::class)->translateFields('Page', $fields, $data['locale']);

                    if (! ($result['success'] ?? false)) {
                        Notification::make()
                            ->title('AI Translation Failed')
                            ->body($result['error'] ?? 'Unknown error occurred.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $langName = $data['locale'] === 'nd' ? 'isiNdebele' : 'ChiShona';
                    $formState = is_array($this->form->getRawState()) ? $this->form->getRawState() : [];
                    foreach (($result['translations'] ?? []) as $field => $translation) {
                        $formState["content_translations_{$data['locale']}_{$field}"] = $translation;
                    }
                    $this->form->fill($formState);

                    Notification::make()
                        ->title("{$langName} Translations Ready")
                        ->body('Review the translations below, then press Create to save them with this page.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
