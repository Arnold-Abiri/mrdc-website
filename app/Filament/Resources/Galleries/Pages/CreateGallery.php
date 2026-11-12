<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Domain\Cms\GalleryManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Models\Gallery;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGallery extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->authorize(fn (): bool => GalleryResource::canCreate())
                ->form([
                    Select::make('locale')->label('Target Language')
                        ->options(['sn' => 'ChiShona', 'nd' => 'isiNdebele'])
                        ->default('sn')->required(),
                ])
                ->action(function (array $data): void {
                    $fields = [];
                    foreach (Gallery::translatableFields() as $field) {
                        $value = $this->data[$field] ?? null;
                        if (is_string($value) && trim($value) !== '') {
                            $fields[$field] = $value;
                        }
                    }

                    if ($fields === []) {
                        Notification::make()->title('No Content to Translate')
                            ->body('Enter the English gallery details first.')->warning()->send();

                        return;
                    }

                    $result = app(GeminiStaffService::class)->translateFields('Gallery', $fields, $data['locale']);
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
                        ->body('Review the translations below, then press Create to save the gallery.')
                        ->success()->send();
                }),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(GalleryManager::class)->create(auth()->user(), $data);
    }
}
