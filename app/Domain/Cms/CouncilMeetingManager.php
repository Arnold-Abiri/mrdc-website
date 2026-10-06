<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Domain\Identity\DataScopeAuthorizer;
use App\Models\CouncilMeeting;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CouncilMeetingManager
{
    public function create(User $actor, array $input): CouncilMeeting
    {
        Gate::forUser($actor)->authorize('create', CouncilMeeting::class);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'meetings.create', $data['department_id'] ?? null, $actor->id), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $data): CouncilMeeting {
            $meeting = new CouncilMeeting($data);
            $meeting->created_by = $actor->id;
            $meeting->updated_by = $actor->id;
            $meeting->save();
            app(AuditWriter::class)->record($actor, 'meeting.created', $meeting, ['title' => $meeting->title]);

            return $meeting;
        });
    }

    public function update(User $actor, CouncilMeeting $meeting, array $input): CouncilMeeting
    {
        Gate::forUser($actor)->authorize('update', $meeting);
        $data = $this->validate($input);
        abort_unless(app(DataScopeAuthorizer::class)->allows($actor, 'meetings.update', $data['department_id'] ?? null, $meeting->created_by), 403);
        $this->authorizeRelations($actor, $data);

        return DB::transaction(function () use ($actor, $meeting, $data): CouncilMeeting {
            $meeting = CouncilMeeting::query()->lockForUpdate()->findOrFail($meeting->id);
            Gate::forUser($actor)->authorize('update', $meeting);
            $meeting->fill($data);
            $meeting->status = 'draft';
            $meeting->verification_status = 'demo';
            $meeting->published_at = null;
            $meeting->verified_by = null;
            $meeting->verified_at = null;
            $meeting->updated_by = $actor->id;
            $meeting->save();
            app(AuditWriter::class)->record($actor, 'meeting.updated', $meeting, ['title' => $meeting->title]);

            return $meeting;
        });
    }

    public function setVerification(User $actor, CouncilMeeting $meeting, string $state): CouncilMeeting
    {
        Gate::forUser($actor)->authorize('verify', $meeting);
        abort_unless(in_array($state, ['demo', 'verified', 'publishable'], true), 422);

        return DB::transaction(function () use ($actor, $meeting, $state): CouncilMeeting {
            $meeting = CouncilMeeting::query()->lockForUpdate()->findOrFail($meeting->id);
            Gate::forUser($actor)->authorize('verify', $meeting);
            $meeting->verification_status = $state;
            $meeting->verified_by = $state === 'demo' ? null : $actor->id;
            $meeting->verified_at = $state === 'demo' ? null : now();
            if ($state !== 'publishable') {
                $meeting->status = 'unpublished';
                $meeting->published_at = null;
            }
            $meeting->updated_by = $actor->id;
            $meeting->save();
            app(AuditWriter::class)->record($actor, 'meeting.verification_changed', $meeting, ['state' => $state]);

            return $meeting;
        });
    }

    public function setStatus(User $actor, CouncilMeeting $meeting, string $status): CouncilMeeting
    {
        Gate::forUser($actor)->authorize('publish', $meeting);
        abort_unless(in_array($status, ['published', 'unpublished', 'archived'], true), 422);

        return DB::transaction(function () use ($actor, $meeting, $status): CouncilMeeting {
            $meeting = CouncilMeeting::query()->lockForUpdate()->findOrFail($meeting->id);
            Gate::forUser($actor)->authorize('publish', $meeting);
            abort_if($status === 'published' && $meeting->verification_status !== 'publishable', 422);
            $meeting->status = $status;
            $meeting->published_at = $status === 'published' ? now() : null;
            $meeting->updated_by = $actor->id;
            $meeting->save();
            app(AuditWriter::class)->record($actor, 'meeting.'.$status, $meeting, ['title' => $meeting->title]);

            return $meeting;
        });
    }

    private function authorizeRelations(User $actor, array $data): void
    {
        foreach (['agenda_document_id', 'minutes_document_id'] as $key) {
            if (! empty($data[$key])) {
                $document = Document::query()->findOrFail($data[$key]);
                Gate::forUser($actor)->authorize('view', $document);
            }
        }
    }

    private function validate(array $input): array
    {
        return Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'meeting_type' => ['required', Rule::in(['full_council', 'committee', 'special', 'public_hearing'])],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['nullable', 'string', 'max:20'],
            'venue' => ['nullable', 'string', 'max:255'],
            'meeting_status' => ['required', Rule::in(['scheduled', 'completed', 'postponed', 'cancelled'])],
            'summary' => ['nullable', 'string', 'max:5000'],
            'agenda_document_id' => ['nullable', 'integer', Rule::exists('documents', 'id')],
            'minutes_document_id' => ['nullable', 'integer', Rule::exists('documents', 'id')],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')->where('status', 'active')],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ])->validate();
    }
}
