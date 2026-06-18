<?php

namespace App\Jobs;

use App\Models\Registration;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendRegistrationTelegramMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Registration $registration,
        public string $stage = 'registration_submitted',
    ) {
    }

    public function handle(): void
    {
        $botToken = (string) config('services.telegram.bot_token');
        $chatId = (string) config('services.telegram.chat_id');
        $apiUrl = rtrim((string) config('services.telegram.api_url', 'https://api.telegram.org'), '/');

        $this->registration->loadMissing([
            'event',
            'payment',
            'finalRegistration',
            'coach',
            'participants',
        ]);

        if (! $botToken || ! $chatId) {
            Log::warning('Registration Telegram notification skipped because configuration is missing.', [
                'registration_id' => $this->registration->id,
                'registration_code' => $this->registration->registration_code,
                'stage' => $this->stage,
                'bot_token_set' => filled($botToken),
                'chat_id_set' => filled($chatId),
            ]);

            return;
        }

        $response = Http::timeout(20)->asForm()->post($apiUrl.'/bot'.$botToken.'/sendMessage', [
            'chat_id' => $chatId,
            'text' => $this->message(),
            'disable_web_page_preview' => true,
        ]);

        Log::info('Registration Telegram notification API response.', [
            'registration_id' => $this->registration->id,
            'registration_code' => $this->registration->registration_code,
            'stage' => $this->stage,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed() || ! (bool) ($response->json('ok') ?? false)) {
            Log::error('Registration Telegram notification sending failed.', [
                'registration_id' => $this->registration->id,
                'registration_code' => $this->registration->registration_code,
                'stage' => $this->stage,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        }
    }

    private function message(): string
    {
        $registration = $this->registration;
        $event = $registration->event;
        $payment = $registration->payment;
        $finalRegistration = $registration->finalRegistration;

        $lines = [
            $this->headline(),
            '',
            'Event',
            'Name: '.$this->value($event?->name),
            'Code: '.$this->value($event?->code),
            '',
            'Registration',
            'Code: '.$this->value($registration->registration_code),
            'Team: '.$this->value($registration->team_name),
            'Institution: '.$this->value($registration->institution),
            'Team Lead: '.$this->value($registration->contact_name),
            'Lead Email: '.$this->value($registration->contact_email),
            'Lead Phone: '.$this->value($registration->contact_phone),
            'Participant Count: '.$registration->participants->count(),
            'Registration Status: '.$this->value($registration->status),
            'Payment Status: '.$this->value($registration->payment_status),
            'Submitted At: '.$this->dateTime($registration->created_at),
            '',
            'Payment Information',
            'Payment Submitted Now: '.($payment ? 'Yes' : 'No'),
            'Amount: '.$this->amount($payment?->amount),
            'Payable Later: '.$this->payableLater(),
            'Method: '.$this->value($payment?->method),
            'TRX ID: '.$this->value($payment?->trx_id),
            'Payment Record Status: '.$this->value($payment?->status),
            'Payment Submitted At: '.$this->dateTime($payment?->submitted_at),
            'Payment Verified At: '.$this->dateTime($payment?->verified_at),
        ];

        if ($finalRegistration) {
            $lines = array_merge($lines, [
                '',
                'Final Registration',
                'Status: '.$this->value($finalRegistration->status),
                'TRX ID: '.$this->value($finalRegistration->trx_id),
                'Submitted At: '.$this->dateTime($finalRegistration->created_at),
                'Updated At: '.$this->dateTime($finalRegistration->updated_at),
                'T-shirt Sizes: '.$this->tshirtSummary(),
            ]);
        }

        if ($registration->coach) {
            $lines = array_merge($lines, [
                '',
                'Coach Information',
                'Name: '.$this->value($registration->coach->name),
                'Designation: '.$this->value($registration->coach->designation),
                'Official Email: '.$this->value($registration->coach->official_email),
                'Contact Number: '.$this->value($registration->coach->contact_number),
                'T-shirt Size: '.$this->value($registration->coach->tshirt_size),
            ]);
        }

        $lines = array_merge($lines, [
            '',
            'Status URL',
            route('registration.status', ['code' => $registration->registration_code]),
        ]);

        return implode("\n", $lines);
    }

    private function headline(): string
    {
        return match ($this->stage) {
            'final_registration_submitted' => 'Final Registration Submitted',
            default => $this->registration->event?->code === '01'
                ? 'New Pre-Registration Submitted'
                : 'New Registration Submitted',
        };
    }

    private function tshirtSummary(): string
    {
        $sizes = $this->registration->participants
            ->pluck('tshirt_size')
            ->filter()
            ->countBy()
            ->map(fn (int $count, string $size) => $size.' x '.$count)
            ->values()
            ->all();

        return $sizes === [] ? '---' : implode(', ', $sizes);
    }

    private function amount(mixed $amount): string
    {
        if ($amount === null || $amount === '') {
            return '---';
        }

        return 'BDT '.number_format((int) $amount);
    }

    private function payableLater(): string
    {
        if ($this->registration->payment || ! ($this->registration->event?->isFinalRoundPaidType() ?? false)) {
            return '---';
        }

        return $this->amount($this->registration->event?->amount);
    }

    private function dateTime(mixed $dateTime): string
    {
        if (! $dateTime instanceof DateTimeInterface) {
            return '---';
        }

        return $dateTime->timezone(config('app.timezone'))->format('d M Y, h:i A');
    }

    private function value(mixed $value): string
    {
        return filled($value) ? (string) $value : '---';
    }
}
