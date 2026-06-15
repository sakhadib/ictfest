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
        $registration->loadMissing(['event', 'coach']);

        try {
            SendRegistrationSms::dispatch(
                $registration,
                (string) config('services.bulk_sms.api_key'),
                (string) config('services.bulk_sms.sender_id'),
                (string) config('services.bulk_sms.url'),
            );

            if (self::shouldNotifyCoach($registration)) {
                SendRegistrationSms::dispatch(
                    $registration,
                    (string) config('services.bulk_sms.api_key'),
                    (string) config('services.bulk_sms.sender_id'),
                    (string) config('services.bulk_sms.url'),
                    'registration_submitted',
                    $registration->coach->contact_number,
                );
            }
        } catch (Throwable $exception) {
            Log::error('Registration confirmation SMS queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'phone' => $registration->contact_phone,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private static function shouldNotifyCoach(Registration $registration): bool
    {
        return $registration->event?->code === '01' && filled($registration->coach?->contact_number);
    }
}
