<?php

namespace App\Filament\Resources\Enquiries\Pages;

use App\Domain\Cms\EnquiryManager;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;

class ViewEnquiry extends ViewRecord
{
    protected static string $resource = EnquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('route')->label('Route to department')->authorize(fn (): bool => Gate::allows('route', $this->record))
                ->form([Select::make('department_id')->label('Department')->options(fn (): array => Department::query()->where('status', 'active')->orderBy('name')->pluck('name', 'id')->all())->required()])
                ->action(fn (array $data) => app(EnquiryManager::class)->route(auth()->user(), $this->enquiry(), Department::query()->findOrFail($data['department_id']))),
            Action::make('assign')->label('Assign staff')->authorize(fn (): bool => Gate::allows('assign', $this->record) && $this->enquiry()->department_id !== null)
                ->form([Select::make('user_id')->label('Staff member')->options(fn (): array => User::query()->where('status', 'active')->where('department_id', $this->enquiry()->department_id)->orderBy('name')->pluck('name', 'id')->all())->required()])
                ->action(fn (array $data) => app(EnquiryManager::class)->assign(auth()->user(), $this->enquiry(), User::query()->findOrFail($data['user_id']))),
            Action::make('note')->label('Add internal note')->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->form([Textarea::make('body')->label('Note')->required()->maxLength(5000)])
                ->action(fn (array $data) => app(EnquiryManager::class)->addNote(auth()->user(), $this->enquiry(), $data['body'])),
            Action::make('in_progress')->label('Mark in progress')->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->requiresConfirmation()->action(fn () => app(EnquiryManager::class)->setStatus(auth()->user(), $this->enquiry(), 'in_progress')),
            Action::make('resolved')->label('Resolve')->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->requiresConfirmation()->action(fn () => app(EnquiryManager::class)->setStatus(auth()->user(), $this->enquiry(), 'resolved')),
            Action::make('closed')->label('Close')->authorize(fn (): bool => Gate::allows('update', $this->record))
                ->requiresConfirmation()->action(fn () => app(EnquiryManager::class)->setStatus(auth()->user(), $this->enquiry(), 'closed')),
        ];
    }

    private function enquiry(): Enquiry
    {
        if (! $this->record instanceof Enquiry) {
            throw new \LogicException('Invalid record.');
        }

        return $this->record;
    }
}
