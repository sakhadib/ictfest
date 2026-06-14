<?php

namespace App\Actions;

use App\Jobs\SendRegistrationSms;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendRegistrationConfirmationSms
{
    public static function queue(Registration $registration): void
    {
        try {
            SendRegistrationSms::dispatch(
                $registration,
                (string) config('services.bulk_sms.api_key'),
                (string) config('services.bulk_sms.sender_id'),
                (string) config('services.bulk_sms.url'),
            );
        } catch (Throwable $exception) {
            Log::error('Registration confirmation SMS queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'phone' => $registration->contact_phone,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
