<?php

namespace App\Notifications;

use App\Filament\Resources\Enquiries\EnquiryResource;
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
        $adminUrl = EnquiryResource::getUrl('index');

        return (new MailMessage)
            ->subject('Enquiry Workflow Notification - '.$this->publicId)
            ->greeting('Hello!')
            ->line('An enquiry requires staff attention.')
            ->line('Reference: '.$this->publicId)
            ->line('Update: '.$this->event)
            ->action('Review in Admin Panel', $adminUrl)
            ->line('Open the council administration panel to review it.')
            ->salutation("Regards,\n".config('mail.from.name', 'Mutoko Rural District Council'));
    }
}
