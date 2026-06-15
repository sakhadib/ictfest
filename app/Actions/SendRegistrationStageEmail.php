<?php

namespace App\Actions;

use App\Mail\RegistrationStageUpdated;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendRegistrationStageEmail
{
    public static function queue(Registration $registration, string $stage): void
    {
        try {
            Mail::to($registration->contact_email, $registration->contact_name)
                ->queue(new RegistrationStageUpdated($registration, $stage));
        } catch (Throwable $exception) {
            Log::error('Registration stage email queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'stage' => $stage,
                'email' => $registration->contact_email,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
