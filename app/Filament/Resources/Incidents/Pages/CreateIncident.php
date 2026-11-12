<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Domain\Operations\IncidentManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Incidents\IncidentResource;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    use HandlesTranslations;

    protected function handleRecordCreation(array $data): Model
    {
        return app(IncidentManager::class)->create(auth()->user(), $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ai_translate')
                ->label('AI Translate')
                ->icon('heroicon-o-sparkles')
                ->form([
                    Textarea::make('source')
                        ->label('English summary')
                        ->default(fn (): string => (string) ($this->form->getState()['summary'] ?? ''))
                        ->rows(3)
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
                        'incident summary',
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
                    $state["content_translations_{$data['locale']}_summary"] = $result['translation'];
                    $this->form->fill($state);

                    Notification::make()
                        ->title("{$langName} Translation Ready")
                        ->body('Review the translation below, then press Create to save it with this incident.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
