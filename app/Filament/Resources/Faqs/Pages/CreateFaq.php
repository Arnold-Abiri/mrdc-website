<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Domain\Cms\FaqManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Faqs\FaqResource;
use App\Models\Faq;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFaq extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->authorize(fn (): bool => FaqResource::canCreate())
                ->form([
                    Select::make('locale')->label('Target Language')
                        ->options(['sn' => 'ChiShona', 'nd' => 'isiNdebele'])
                        ->default('sn')->required(),
                ])
                ->action(function (array $data): void {
                    $fields = [];
                    foreach (Faq::translatableFields() as $field) {
                        $value = $this->data[$field] ?? null;
                        if (is_string($value) && trim($value) !== '') {
                            $fields[$field] = $value;
                        }
                    }

                    if ($fields === []) {
                        Notification::make()->title('No Content to Translate')
                            ->body('Enter the English question or answer first.')->warning()->send();

                        return;
                    }

                    $result = app(GeminiStaffService::class)->translateFields('Faq', $fields, $data['locale']);
                    if (! ($result['success'] ?? false)) {
                        Notification::make()->title('AI Translation Failed')
                            ->body($result['error'] ?? 'Unable to generate translations.')->danger()->send();

                        return;
                    }

                    foreach ($fields as $field => $value) {
                        $translation = $result['translations'][$field] ?? null;
                        if (is_string($translation) && trim($translation) !== '') {
                            $this->data["content_translations_{$data['locale']}_{$field}"] = $translation;
                        }
                    }

                    Notification::make()->title('Translations ready')
                        ->body('Review the translations below, then press Create to save the faq.')
                        ->success()->send();
                }),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(FaqManager::class)->create(auth()->user(), $data);
    }
}
