<?php

namespace App\Mail;

use App\Models\IupcCoachLink;
use App\Queue\Middleware\ThrottleResendEmails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IupcCoachPortalLinkMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 0;

    public function __construct(
        public IupcCoachLink $coachLink,
        public string $url,
    ) {
        $this->coachLink->loadMissing(['allocation', 'coach']);
    }

    public function middleware(): array
    {
        return [
            new ThrottleResendEmails(),
        ];
    }

    public function retryUntil(): \DateTimeInterface
    {
        return now()->addHours(6);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IUPC team final registration link - '.$this->coachLink->allocation?->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.iupc-coach-portal-link',
        );
    }
}
