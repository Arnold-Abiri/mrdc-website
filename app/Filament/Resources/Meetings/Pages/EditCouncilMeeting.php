<?php

namespace App\Filament\Resources\Meetings\Pages;

use App\Domain\Cms\CouncilMeetingManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Meetings\CouncilMeetingResource;
use App\Models\CouncilMeeting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditCouncilMeeting extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = CouncilMeetingResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof CouncilMeeting, 404);

        $updatedRecord = app(CouncilMeetingManager::class)->update(auth()->user(), $record, $data);
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
                    ->visible(fn (): bool => $this->councilMeeting()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(CouncilMeetingManager::class)->setVerification(auth()->user(), $this->councilMeeting(), 'publishable');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->councilMeeting()->status !== 'published')
                    ->disabled(fn (): bool => $this->councilMeeting()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->councilMeeting()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(CouncilMeetingManager::class)->setStatus(auth()->user(), $this->councilMeeting(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->councilMeeting()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(CouncilMeetingManager::class)->setStatus(auth()->user(), $this->councilMeeting(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            DeleteAction::make(),
        ];
    }

    private function councilMeeting(): CouncilMeeting
    {
        abort_unless($this->record instanceof CouncilMeeting, 404);

        return $this->record;
    }
}
