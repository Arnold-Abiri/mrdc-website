<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\Department;
use App\Models\Enquiry;
use App\Models\User;
use App\Notifications\EnquiryWorkflowNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class EnquiryManager
{
    private const TRANSITIONS = [
        'new' => ['in_progress', 'closed'],
        'in_progress' => ['resolved', 'closed'],
        'resolved' => ['in_progress', 'closed'],
        'closed' => ['in_progress'],
    ];

    public function submit(array $input): Enquiry
    {
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:40'],
            'category' => ['required', Rule::in(['general', 'services', 'feedback', 'other'])],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'size:0'],
        ])->validate();
        unset($data['website']);
        $enquiry = new Enquiry($data);
        $enquiry->public_id = (string) Str::uuid();
        $enquiry->status = 'new';
        $enquiry->submitted_at = now();
        $enquiry->save();

        return $enquiry;
    }

    public function setStatus(User $actor, Enquiry $enquiry, string $status): Enquiry
    {
        Gate::forUser($actor)->authorize('update', $enquiry);

        $updated = DB::transaction(function () use ($actor, $enquiry, $status): Enquiry {
            $enquiry = Enquiry::query()->lockForUpdate()->findOrFail($enquiry->id);
            Gate::forUser($actor)->authorize('update', $enquiry);
            $before = $enquiry->status;
            abort_unless(in_array($status, self::TRANSITIONS[$before] ?? [], true), 422, 'Invalid enquiry status transition.');
            $enquiry->status = $status;
            $enquiry->save();
            app(AuditWriter::class)->record($actor, 'enquiries.status_changed', $enquiry, ['from' => $before, 'to' => $status]);

            return $enquiry;
        });

        $this->notifyStaff($this->staffForDepartment($updated->department_id), $updated, 'Status changed to '.$updated->status);

        return $updated;
    }

    public function route(User $actor, Enquiry $enquiry, Department $department): Enquiry
    {
        Gate::forUser($actor)->authorize('route', $enquiry);
        abort_unless($department->status === 'active', 422);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'enquiries.route', $department->id, null), 403);

        $updated = DB::transaction(function () use ($actor, $enquiry, $department): Enquiry {
            $enquiry = Enquiry::query()->lockForUpdate()->findOrFail($enquiry->id);
            Gate::forUser($actor)->authorize('route', $enquiry);
            abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'enquiries.route', $department->id, null), 403);
            $previousDepartment = $enquiry->department_id;
            $enquiry->department_id = $department->id;
            $enquiry->assigned_to = null;
            $enquiry->save();
            app(AuditWriter::class)->record($actor, 'enquiries.routed', $enquiry, ['from_department_id' => $previousDepartment, 'to_department_id' => $department->id]);

            return $enquiry;
        });

        $this->notifyStaff($this->staffForDepartment($updated->department_id), $updated, 'Enquiry routed to a department');

        return $updated;
    }

    public function assign(User $actor, Enquiry $enquiry, User $assignee): Enquiry
    {
        Gate::forUser($actor)->authorize('assign', $enquiry);

        $previousAssignee = $enquiry->assigned_to;
        $updated = DB::transaction(function () use ($actor, $enquiry, $assignee, $previousAssignee): Enquiry {
            $enquiry = Enquiry::query()->lockForUpdate()->findOrFail($enquiry->id);
            Gate::forUser($actor)->authorize('assign', $enquiry);
            $assignee->refresh();
            abort_unless($enquiry->department_id !== null && $assignee->status === 'active' && $assignee->department_id === $enquiry->department_id, 422, 'Assignee must be active in the routed department.');
            abort_unless(app(DataScopeAuthorizer::class)->allows($assignee, 'enquiries.view', $enquiry->department_id, null), 422, 'Assignee cannot view this enquiry.');
            $enquiry->assigned_to = $assignee->id;
            $enquiry->save();
            app(AuditWriter::class)->record($actor, 'enquiries.assigned', $enquiry, ['from_user_id' => $previousAssignee, 'to_user_id' => $assignee->id]);

            return $enquiry;
        });

        $recipients = $this->staffForDepartment($updated->department_id)
            ->whereIn('id', array_filter([$updated->assigned_to, $previousAssignee ?? null]))
            ->values();
        $this->notifyStaff($recipients, $updated, $updated->assigned_to === $previousAssignee ? 'Enquiry assignment confirmed' : 'Enquiry assigned or reassigned');

        return $updated;
    }

    /** @return Collection<int, User> */
    private function staffForDepartment(?int $departmentId): Collection
    {
        if ($departmentId === null) {
            return collect();
        }

        return User::query()->where('department_id', $departmentId)->where('status', 'active')->get()
            ->filter(fn (User $staff): bool => app(DataScopeAuthorizer::class)->allows($staff, 'enquiries.view', $departmentId, null))
            ->values();
    }

    /** @param Collection<int, User> $recipients */
    private function notifyStaff(Collection $recipients, Enquiry $enquiry, string $event): void
    {
        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send($recipients, new EnquiryWorkflowNotification($enquiry->public_id, $event));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function addNote(User $actor, Enquiry $enquiry, string $body): void
    {
        Gate::forUser($actor)->authorize('update', $enquiry);
        $data = Validator::make(['body' => $body], ['body' => ['required', 'string', 'max:5000']])->validate();

        DB::transaction(function () use ($actor, $enquiry, $data): void {
            $enquiry = Enquiry::query()->lockForUpdate()->findOrFail($enquiry->id);
            Gate::forUser($actor)->authorize('update', $enquiry);
            $enquiry->notes()->create(['author_id' => $actor->id, 'body' => $data['body']]);
            app(AuditWriter::class)->record($actor, 'enquiries.note_added', $enquiry);
        });
    }
}
