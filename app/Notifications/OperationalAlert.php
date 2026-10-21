<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperationalAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $subject, public string $summary)
    {
        $this->afterCommit();
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject.' - '.config('mail.from.name', 'Mutoko Rural District Council'))
            ->greeting('Hello!')
            ->line($this->summary)
            ->line('Open the council administration panel, System health, for details.')
            ->salutation("Regards,\n".config('mail.from.name', 'Mutoko Rural District Council'));
    }
}
