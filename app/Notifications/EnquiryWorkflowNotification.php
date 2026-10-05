<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnquiryWorkflowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $publicId, public string $event)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Council enquiry workflow update')
            ->line('An enquiry requires staff attention.')
            ->line('Reference: '.$this->publicId)
            ->line('Update: '.$this->event)
            ->line('Open the council administration panel to review it.');
    }
}
