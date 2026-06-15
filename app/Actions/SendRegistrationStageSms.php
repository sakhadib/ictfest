<?php

namespace App\Actions;

use App\Jobs\SendRegistrationSms;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendRegistrationStageSms
{
    public static function queue(Registration $registration, string $stage): void
    {
        try {
            SendRegistrationSms::dispatch(
                $registration,
                (string) config('services.bulk_sms.api_key'),
                (string) config('services.bulk_sms.sender_id'),
                (string) config('services.bulk_sms.url'),
                $stage,
            );
        } catch (Throwable $exception) {
            Log::error('Registration stage SMS queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'stage' => $stage,
                'phone' => $registration->contact_phone,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
