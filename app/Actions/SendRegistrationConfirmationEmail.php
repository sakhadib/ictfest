<?php

namespace App\Actions;

use App\Mail\RegistrationSubmitted;
use App\Models\Registration;
use App\Rules\StrictEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendRegistrationConfirmationEmail
{
    public static function queue(Registration $registration): void
    {
        $registration->loadMissing(['event', 'coach']);

        try {
            self::queueRecipient($registration, $registration->contact_email, $registration->contact_name, 'team_lead');

            if (self::shouldNotifyCoach($registration)) {
                self::queueRecipient($registration, $registration->coach->official_email, $registration->coach->name, 'coach');
            }
        } catch (Throwable $exception) {
            Log::error('Registration confirmation email queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'email' => $registration->contact_email,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private static function shouldNotifyCoach(Registration $registration): bool
    {
        return $registration->event?->code === '01' && filled($registration->coach?->official_email);
    }

    private static function queueRecipient(Registration $registration, ?string $email, ?string $name, string $recipientType): void
    {
        if (! StrictEmail::isValid($email)) {
            Log::warning('Registration confirmation email skipped because recipient email is invalid.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'recipient_type' => $recipientType,
                'email' => $email,
            ]);

            return;
        }

        Mail::to(trim($email), $name)
            ->queue(new RegistrationSubmitted($registration));
    }
}
