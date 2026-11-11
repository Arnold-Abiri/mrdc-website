<?php

namespace App\Filament\Resources\Enquiries\Pages;

use App\Domain\Cms\EnquiryManager;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use App\Services\Ai\GeminiStaffService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ViewEnquiry extends ViewRecord
{
    protected static string $resource = EnquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('route')->label('Route to department')->authorize(fn (): bool => Gate::allows('route', $this->record))
                ->form([Select::make('department_id')->label('Department')->options(fn (): array => Department::query()->where('status', 'active')->orderBy('name')->pluck('name', 'id')->all())->required()])
                ->action(function (array $data): void {
                    app(EnquiryManager::class)->route(auth()->user(), $this->enquiry(), Department::query()->findOrFail($data['department_id']));
                    $this->refreshRecord();
                    Notification::make()->title('Enquiry routed')->success()->send();
                }),
            Action::make('assign')->label('Assign staff')->authorize(fn (): bool => Gate::allows('assign', $this->record) && $this->enquiry()->department_id !== null)
                ->form([Select::make('user_id')->label('Staff member')->options(fn (): array => User::query()->where('status', 'active')->where('department_id', $this->enquiry()->department_id)->orderBy('name')->pluck('name', 'id')->all())->required()])
                ->action(function (array $data): void {
                    app(EnquiryManager::class)->assign(auth()->user(), $this->enquiry(), User::query()->findOrFail($data['user_id']));
                    $this->refreshRecord();
                    Notification::make()->title('Staff member assigned')->success()->send();
                }),
            Action::make('note')->label('Add internal note')->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->form([Textarea::make('body')->label('Note')->required()->maxLength(5000)])
                ->action(function (array $data): void {
                    app(EnquiryManager::class)->addNote(auth()->user(), $this->enquiry(), $data['body']);
                    $this->refreshRecord();
                    Notification::make()->title('Note added')->success()->send();
                }),
            Action::make('ai_draft_response')
                ->label('AI Draft Reply')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->form([
                    Select::make('language')
                        ->label('Response Language')
                        ->options([
                            'en' => 'English',
                            'sn' => 'ChiShona',
                            'nd' => 'isiNdebele',
                        ])
                        ->default('en')
                        ->required(),
                    Textarea::make('officer_guidance')
                        ->label('Key Officer Points / Action Taken')
                        ->placeholder('e.g. Plumbing crew dispatched, leak isolated, parts will arrive tomorrow.')
                        ->rows(3)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $enquiry = $this->enquiry();
                    $service = app(GeminiStaffService::class);
                    $result = $service->draftEnquiryResponse(
                        [
                            'name' => $enquiry->name,
                            'subject' => $enquiry->subject,
                            'message' => $enquiry->message,
                            'category' => $enquiry->category,
                        ],
                        $data['officer_guidance'],
                        $data['language']
                    );

                    if (! ($result['success'] ?? false)) {
                        Notification::make()
                            ->title('AI Drafting Failed')
                            ->body($result['error'] ?? 'Unable to draft message.')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Save as internal note so officer can review, copy, or adapt
                    app(EnquiryManager::class)->addNote(
                        auth()->user(),
                        $enquiry,
                        "[AI Drafted Response ({$data['language']})]:\n".$result['response']
                    );

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Draft Generated & Added to Internal Notes')
                        ->body('The draft was saved to Internal Notes and automatically prefilled into "Send Update to Citizen" for review.')
                        ->success()
                        ->send();
                }),
            Action::make('notify_citizen')
                ->label('Send Update to Citizen')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->form([
                    Textarea::make('message')
                        ->label('Official Officer Response / Update Note')
                        ->placeholder('Explain current progress, actions taken, or steps required from the citizen...')
                        ->default(function (): ?string {
                            $latestAiNote = $this->enquiry()->notes()
                                ->where('body', 'like', '[AI Drafted Response%')
                                ->latest()
                                ->first();

                            if (! $latestAiNote) {
                                return null;
                            }

                            // Extract the actual response body after the header tag
                            return preg_replace('/^\[AI Drafted Response \([a-z]+\)\]:\s*/', '', $latestAiNote->body);
                        })
                        ->rows(6)
                        ->required()
                        ->maxLength(5000)
                        ->helperText('This message will be emailed directly to '.$this->enquiry()->email.' along with the reference number and current status. If you generated an AI Draft, it is prefilled here for you to review and edit.'),
                ])
                ->action(function (array $data): void {
                    app(EnquiryManager::class)->notifyCitizen(auth()->user(), $this->enquiry(), $data['message']);
                    $this->refreshRecord();
                    Notification::make()
                        ->title('Notification dispatched to citizen')
                        ->success()
                        ->send();
                }),
            ActionGroup::make([
                Action::make('in_progress')->label('Mark in progress')->authorize(fn (): bool => Gate::allows('update', $this->record))
                    ->visible(fn (): bool => EnquiryManager::canTransition($this->enquiry()->status, 'in_progress'))
                    ->requiresConfirmation()->action(function (): void {
                        $this->changeStatus('in_progress', 'Marked in progress');
                    }),
                Action::make('resolved')->label('Resolve')->authorize(fn (): bool => Gate::allows('update', $this->record))
                    ->visible(fn (): bool => EnquiryManager::canTransition($this->enquiry()->status, 'resolved'))
                    ->requiresConfirmation()->action(function (): void {
                        $this->changeStatus('resolved', 'Enquiry resolved');
                    }),
                Action::make('closed')->label('Close')->authorize(fn (): bool => Gate::allows('update', $this->record))
                    ->visible(fn (): bool => EnquiryManager::canTransition($this->enquiry()->status, 'closed'))
                    ->requiresConfirmation()->action(function (): void {
                        $this->changeStatus('closed', 'Enquiry closed');
                    }),
            ])->label('Change status')->button()->outlined(),
        ];
    }

    private function enquiry(): Enquiry
    {
        if (! $this->record instanceof Enquiry) {
            throw new \LogicException('Invalid record.');
        }

        return $this->record;
    }

    private function refreshRecord(): void
    {
        $this->record->refresh();
    }

    private function changeStatus(string $status, string $successTitle): void
    {
        try {
            app(EnquiryManager::class)->setStatus(auth()->user(), $this->enquiry(), $status);
        } catch (HttpException $exception) {
            $this->refreshRecord();
            Notification::make()
                ->title('Status no longer available')
                ->body('This enquiry changed before the action was confirmed. The view has been refreshed.')
                ->warning()
                ->send();

            return;
        }

        $this->refreshRecord();
        Notification::make()->title($successTitle)->success()->send();
    }
}
