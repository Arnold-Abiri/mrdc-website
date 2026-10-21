<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Enquiry $enquiry,
        public ?string $officerNote = null,
        public ?string $statusLabel = null
    ) {
        $this->statusLabel = $statusLabel ?? str($enquiry->status)->headline()->toString();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $shortRef = substr((string) ($this->enquiry->public_id ?? ''), 0, 8);

        return new Envelope(
            subject: 'Update on Your Enquiry'.($shortRef !== '' ? ' ['.$shortRef.']' : '').' - Mutoko RDC',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enquiries.status-updated',
            with: [
                'enquiry' => $this->enquiry,
                'officerNote' => $this->officerNote,
                'statusLabel' => $this->statusLabel,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
