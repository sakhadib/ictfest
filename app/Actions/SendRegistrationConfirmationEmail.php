<?php

namespace App\Actions;

use App\Mail\RegistrationSubmitted;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendRegistrationConfirmationEmail
{
    public static function queue(Registration $registration): void
    {
        try {
            Mail::to($registration->contact_email, $registration->contact_name)
                ->queue(new RegistrationSubmitted($registration));
        } catch (Throwable $exception) {
            Log::error('Registration confirmation email queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'email' => $registration->contact_email,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
