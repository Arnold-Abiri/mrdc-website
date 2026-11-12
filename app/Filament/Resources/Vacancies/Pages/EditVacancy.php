<?php

namespace App\Filament\Resources\Vacancies\Pages;

use App\Domain\Cms\VacancyManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Concerns\PreviewHelper;
use App\Filament\Resources\Vacancies\VacancyResource;
use App\Models\Vacancy;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditVacancy extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = VacancyResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Vacancy, 404);

        $updatedRecord = app(VacancyManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            self::makeAiTranslateAction(),
            ActionGroup::make([
                Action::make('verify')
                    ->label('Mark publishable')
                    ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                    ->visible(fn (): bool => $this->vacancy()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(VacancyManager::class)->setVerification(auth()->user(), $this->vacancy(), 'publishable');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->vacancy()->status !== 'published')
                    ->disabled(fn (): bool => $this->vacancy()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->vacancy()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(VacancyManager::class)->setStatus(auth()->user(), $this->vacancy(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->vacancy()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(VacancyManager::class)->setStatus(auth()->user(), $this->vacancy(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            PreviewHelper::makePreviewAction(),
            DeleteAction::make(),
        ];
    }

    private function vacancy(): Vacancy
    {
        abort_unless($this->record instanceof Vacancy, 404);

        return $this->record;
    }
}
