<?php

namespace App\Mail;

use App\Queue\Middleware\ThrottleResendEmails;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 0;

    public function __construct(
        public Registration $registration,
    ) {
        $this->registration->loadMissing(['event', 'participants', 'payment']);
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
            subject: $this->registration->event?->isFinalRoundPaidType()
                ? 'Pre-Registration received: '.$this->registration->registration_code
                : 'Registration received: '.$this->registration->registration_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-submitted',
        );
    }
}
