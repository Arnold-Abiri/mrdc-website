<?php

namespace App\Filament\Resources\Investment\Pages;

use App\Domain\Cms\InvestmentManager;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\Investment\InvestmentOpportunityResource;
use App\Models\InvestmentOpportunity;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditInvestmentOpportunity extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = InvestmentOpportunityResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof InvestmentOpportunity, 404);

        $updatedRecord = app(InvestmentManager::class)->update(auth()->user(), $record, $data);
        $this->record = $updatedRecord->refresh();

        return $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('verify')
                    ->label('Mark publishable')
                    ->authorize(fn (): bool => Gate::allows('verify', $this->record))
                    ->visible(fn (): bool => $this->investmentOpportunity()->verification_status !== 'publishable')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(InvestmentManager::class)->setVerification(auth()->user(), $this->investmentOpportunity(), 'publishable');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('publish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->investmentOpportunity()->status !== 'published')
                    ->disabled(fn (): bool => $this->investmentOpportunity()->verification_status !== 'publishable')
                    ->tooltip(fn (): ?string => $this->investmentOpportunity()->verification_status !== 'publishable' ? 'Mark this record publishable before publishing.' : null)
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(InvestmentManager::class)->setStatus(auth()->user(), $this->investmentOpportunity(), 'published');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
                Action::make('unpublish')
                    ->authorize(fn (): bool => Gate::allows('publish', $this->record))
                    ->visible(fn (): bool => $this->investmentOpportunity()->status === 'published')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        app(InvestmentManager::class)->setStatus(auth()->user(), $this->investmentOpportunity(), 'unpublished');
                        $this->record = $this->record->refresh();
                        $this->refreshFormData(['status', 'verification_status', 'published_at']);
                    }),
            ])->label('Publishing')->button()->outlined(),
            DeleteAction::make(),
        ];
    }

    private function investmentOpportunity(): InvestmentOpportunity
    {
        abort_unless($this->record instanceof InvestmentOpportunity, 404);

        return $this->record;
    }
}
