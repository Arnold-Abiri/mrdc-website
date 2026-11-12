<?php

namespace App\Filament\Resources\Editorial\Pages;

use App\Domain\Cms\EditorialManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Editorial\EditorialItemResource;
use App\Models\EditorialItem;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEditorialItem extends CreateRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = EditorialItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->authorize(fn (): bool => EditorialItemResource::canCreate())
                ->form([
                    Select::make('locale')->label('Target Language')
                        ->options(['sn' => 'ChiShona', 'nd' => 'isiNdebele'])
                        ->default('sn')->required(),
                ])
                ->action(function (array $data): void {
                    $fields = [];
                    foreach (EditorialItem::translatableFields() as $field) {
                        $value = $this->data[$field] ?? null;
                        if (is_string($value) && trim($value) !== '') {
                            $fields[$field] = $value;
                        }
                    }

                    if ($fields === []) {
                        Notification::make()->title('No Content to Translate')
                            ->body('Enter the English editorial text first.')->warning()->send();

                        return;
                    }

                    $result = app(GeminiStaffService::class)->translateEditorial(
                        $fields['title'] ?? '',
                        $fields['summary'] ?? '',
                        $fields['body'] ?? '',
                        $fields['seo_title'] ?? null,
                        $fields['meta_description'] ?? null,
                        $data['locale'],
                    );
                    if (! ($result['success'] ?? false)) {
                        Notification::make()->title('AI Translation Failed')
                            ->body($result['error'] ?? 'Unable to generate translations.')->danger()->send();

                        return;
                    }

                    foreach ($fields as $field => $value) {
                        $translation = $result[$field] ?? null;
                        if (is_string($translation) && trim($translation) !== '') {
                            $this->data["content_translations_{$data['locale']}_{$field}"] = $translation;
                        }
                    }

                    Notification::make()->title('Translations ready')
                        ->body('Review the translations below, then press Create to save the editorial item.')
                        ->success()->send();
                }),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data = $this->attachInlineFeaturedImage($data, $data['title'] ?? null);

        return app(EditorialManager::class)->create(auth()->user(), $data);
    }
}
