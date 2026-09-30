<?php

namespace App\Domain\Identity;

use App\Models\AuditEvent;
use App\Models\User;

class AuditWriter
{
    public function record(?User $actor, string $action, object $subject, array $metadata = []): void
    {
        $metadata = $this->redact($metadata);
        $event = new AuditEvent;
        $event->actor_id = $actor?->id;
        $event->action = $action;
        $event->subject_type = $subject::class;
        $event->subject_id = (string) $subject->getKey();
        $event->setAttribute('metadata', $metadata);
        $event->save();
    }

    private function redact(array $values): array
    {
        $safe = [];
        foreach ($values as $key => $value) {
            if (preg_match('/password|token|secret|session|cookie|hash/i', (string) $key)) {
                continue;
            }
            $safe[$key] = is_array($value) ? $this->redact($value) : $value;
        }

        return $safe;
    }
}
