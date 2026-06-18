<?php

namespace App\Actions;

use App\Jobs\SendRegistrationTelegramMessage;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendRegistrationTelegramNotification
{
    public static function queue(Registration $registration, string $stage = 'registration_submitted'): void
    {
        try {
            SendRegistrationTelegramMessage::dispatch($registration, $stage);
        } catch (Throwable $exception) {
            Log::error('Registration Telegram notification queueing failed.', [
                'registration_id' => $registration->id,
                'registration_code' => $registration->registration_code,
                'stage' => $stage,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
