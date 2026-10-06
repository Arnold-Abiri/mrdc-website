<?php

namespace App\Domain\Operations;

use App\Models\Incident;
use App\Notifications\OperationalAlert;
use Illuminate\Support\Facades\Notification;
use Throwable;

class DowntimeAlerter
{
    /**
     * Notify configured operations recipients about an incident.
     * Recipients come from ops.alert_recipients (never hard-coded).
     * Safe transports (array/log) are used outside production.
     */
    public function notify(Incident $incident): void
    {
        $recipients = config('ops.alert_recipients', []);
        if (! is_array($recipients) || $recipients === []) {
            return;
        }
        $summary = sprintf(
            'Incident #%d (%s): %s — started %s.',
            $incident->id,
            $incident->status,
            $incident->summary ?? 'no summary recorded',
            $incident->started_at
        );
        try {
            foreach ($recipients as $address) {
                if (! is_string($address) || trim($address) === '') {
                    continue;
                }
                Notification::route('mail', $address)->notify(new OperationalAlert('Mutoko RDC website incident', $summary));
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
