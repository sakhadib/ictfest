<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationStageUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registration $registration,
        public string $stage,
    ) {
        $this->registration->loadMissing(['event', 'participants', 'payment', 'finalRegistration']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->stageContent()['subject'].' '.$this->registration->registration_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-stage-updated',
            with: [
                'content' => $this->stageContent(),
            ],
        );
    }

    /**
     * @return array{subject: string, heading: string, body: string, button: string, url: string}
     */
    public function stageContent(): array
    {
        return match ($this->stage) {
            'final_qualified' => [
                'subject' => 'Final round qualification:',
                'heading' => 'You are qualified for the final round',
                'body' => 'Your team has been approved for the final round. Submit your final payment and T-shirt information to complete the next step.',
                'button' => 'Submit Final Details',
                'url' => route('final-registration.show', ['registration_code' => $this->registration->registration_code]),
            ],
            'final_payment_confirmed' => [
                'subject' => 'Final payment confirmed:',
                'heading' => 'Your final payment is confirmed',
                'body' => 'Your final round payment and submitted details have been approved.',
                'button' => 'Check Status',
                'url' => route('registration.status', ['code' => $this->registration->registration_code]),
            ],
            'initial_payment_confirmed' => [
                'subject' => 'Registration and payment approved:',
                'heading' => 'Your registration and payment are approved',
                'body' => 'Your registration payment has been verified. Submit your T-shirt information to complete final intake.',
                'button' => 'Submit T-shirt Details',
                'url' => route('final-registration.show', ['registration_code' => $this->registration->registration_code]),
            ],
            'final_intake_confirmed' => [
                'subject' => 'Final confirmation:',
                'heading' => 'Your final intake is confirmed',
                'body' => 'Your T-shirt information has been verified and your team intake is approved.',
                'button' => 'Check Status',
                'url' => route('registration.status', ['code' => $this->registration->registration_code]),
            ],
            default => [
                'subject' => 'Registration update:',
                'heading' => 'Your registration has been updated',
                'body' => 'There is an update for your registration.',
                'button' => 'Check Status',
                'url' => route('registration.status', ['code' => $this->registration->registration_code]),
            ],
        };
    }
}
