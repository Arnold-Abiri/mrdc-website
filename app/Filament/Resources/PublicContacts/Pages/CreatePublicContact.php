<?php

namespace App\Filament\Resources\PublicContacts\Pages;

use App\Domain\Cms\PublicContactManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PublicContacts\PublicContactResource;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePublicContact extends CreateRecord
{
    protected static string $resource = PublicContactResource::class;

    use HandlesTranslations;

    protected function handleRecordCreation(array $data): Model
    {
        return app(PublicContactManager::class)->create(auth()->user(), $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->form([
                    Textarea::make('source')
                        ->label('English office name')
                        ->default(fn (): string => (string) ($this->form->getState()['office'] ?? ''))
                        ->rows(2)
                        ->required(),
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
                    $result = app(GeminiStaffService::class)->translateField(
                        $data['source'],
                        'office name',
                        $data['locale']
                    );

                    if (! ($result['success'] ?? false) || trim((string) ($result['translation'] ?? '')) === '') {
                        Notification::make()
                            ->title('AI Translation Failed')
                            ->body($result['error'] ?? 'Unknown error occurred.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $langName = $data['locale'] === 'nd' ? 'isiNdebele' : 'ChiShona';
                    $state = $this->form->getState();
                    $state["content_translations_{$data['locale']}_office"] = $result['translation'];
                    $this->form->fill($state);

                    Notification::make()
                        ->title("{$langName} Translation Ready")
                        ->body('Review the translation below, then press Create to save it with this contact.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
