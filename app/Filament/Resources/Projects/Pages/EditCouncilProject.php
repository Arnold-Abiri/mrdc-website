<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Domain\Cms\CouncilProjectManager;
use App\Filament\Concerns\HandlesFeaturedImageUpload;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Projects\CouncilProjectResource;
use App\Models\CouncilProject;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditCouncilProject extends EditRecord
{
    use HandlesFeaturedImageUpload;
    use HandlesTranslations;

    protected static string $resource = CouncilProjectResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof CouncilProject, 404);

        $data = $this->attachInlineFeaturedImage($data, $data['title'] ?? $record->title);

        $updatedRecord = app(CouncilProjectManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Mark publishable')
                ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                ->visible(fn (): bool => $this->councilProject()->verification_status !== 'publishable')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(CouncilProjectManager::class)->setVerification(auth()->user(), $this->councilProject(), 'publishable');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('publish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->councilProject()->status !== 'published')
                ->disabled(fn (): bool => $this->councilProject()->verification_status !== 'publishable')
                ->tooltip(fn (): ?string => $this->councilProject()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                ->requiresConfirmation()
                ->action(function (): void {
                    app(CouncilProjectManager::class)->setStatus(auth()->user(), $this->councilProject(), 'published');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            Action::make('unpublish')
                ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                ->visible(fn (): bool => $this->councilProject()->status === 'published')
                ->requiresConfirmation()
                ->action(function (): void {
                    app(CouncilProjectManager::class)->setStatus(auth()->user(), $this->councilProject(), 'unpublished');
                    $this->record = $this->record->refresh();
                    $this->refreshFormData(['status', 'verification_status', 'published_at']);
                }),
            DeleteAction::make(),
        ];
    }

    private function councilProject(): CouncilProject
    {
        abort_unless($this->record instanceof CouncilProject, 404);

        return $this->record;
    }
}
