<?php

namespace App\Filament\Resources\Slides\Pages;

use App\Domain\Cms\HomepageSlideManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Slides\HomepageSlideResource;
use App\Models\HomepageSlide;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateHomepageSlide extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = HomepageSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->authorize(fn (): bool => HomepageSlideResource::canCreate())
                ->form([
                    Select::make('locale')->label('Target Language')
                        ->options(['sn' => 'ChiShona', 'nd' => 'isiNdebele'])
                        ->default('sn')->required(),
                ])
                ->action(function (array $data): void {
                    $fields = [];
                    foreach (HomepageSlide::translatableFields() as $field) {
                        $value = $this->data[$field] ?? null;
                        if (is_string($value) && trim($value) !== '') {
                            $fields[$field] = $value;
                        }
                    }

                    if ($fields === []) {
                        Notification::make()->title('No Content to Translate')
                            ->body('Enter the English slide text first.')->warning()->send();

                        return;
                    }

                    $result = app(GeminiStaffService::class)->translateFields('HomepageSlide', $fields, $data['locale']);
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
                        ->body('Review the translations below, then press Create to save the slide.')
                        ->success()->send();
                }),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineMedia(
            $data,
            uploadKey: 'slide_image_upload',
            targetKey: 'media_id',
            altKey: 'slide_image_alt',
            fallbackTitle: $data['headline'] ?? null,
            titleSuffix: ' slide image',
        );

        return app(HomepageSlideManager::class)->create(auth()->user(), $data);
    }
}
