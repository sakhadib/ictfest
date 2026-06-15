<?php

namespace App\Jobs;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendRegistrationSms implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Registration $registration,
        public string $apiKey,
        public string $senderId,
        public string $url,
        public string $stage = 'registration_submitted',
    ) {
    }

    public function handle(): void
    {
        $this->registration->loadMissing('event');

        $number = $this->normalizePhoneNumber($this->registration->contact_phone);

        if (! $this->apiKey || ! $this->senderId || ! $this->url || ! $number) {
            Log::warning('Registration confirmation SMS skipped because configuration or phone number is missing.', [
                'registration_id' => $this->registration->id,
                'registration_code' => $this->registration->registration_code,
                'phone' => $this->registration->contact_phone,
                'normalized_phone' => $number,
            ]);

            return;
        }

        $message = $this->message();

        Log::info('Registration confirmation SMS dispatch attempt.', [
            'registration_id' => $this->registration->id,
            'registration_code' => $this->registration->registration_code,
            'phone' => $number,
            'event' => $this->registration->event?->name,
            'sender_id' => $this->senderId,
        ]);

        $response = Http::timeout(20)->asForm()->post($this->url, [
            'api_key' => $this->apiKey,
            'number' => $number,
            'senderid' => $this->senderId,
            'message' => $message,
        ]);

        $providerPayload = $this->parseProviderResponse($response->body());
        $providerCode = (string) ($providerPayload['response_code'] ?? trim($response->body()));
        $providerMessage = $this->mapProviderCode($providerCode);

        Log::info('Registration confirmation SMS API response.', [
            'registration_id' => $this->registration->id,
            'registration_code' => $this->registration->registration_code,
            'phone' => $number,
            'status' => $response->status(),
            'body' => $response->body(),
            'provider_code' => $providerCode,
            'provider_message' => $providerMessage,
            'provider_payload' => $providerPayload,
        ]);

        if ($response->failed() || $providerCode !== '202') {
            Log::error('Registration confirmation SMS sending failed.', [
                'registration_id' => $this->registration->id,
                'registration_code' => $this->registration->registration_code,
                'phone' => $number,
                'status' => $response->status(),
                'response' => $response->body(),
                'provider_code' => $providerCode,
                'provider_message' => $providerMessage,
                'provider_payload' => $providerPayload,
            ]);
        }
    }

    private function normalizePhoneNumber(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '880')) {
            return $digits;
        }

        if (str_starts_with($digits, '01')) {
            return '880'.substr($digits, 1);
        }

        if (str_starts_with($digits, '1')) {
            return '880'.$digits;
        }

        return null;
    }

    private function message(): string
    {
        $event = $this->registration->event?->name ?? 'ICT Fest';
        $code = $this->registration->registration_code;

        return match ($this->stage) {
            'final_qualified' => $this->registration->participants->count() === 1
                ? sprintf(
                    'Congratulations! You are qualified for %s. Payable amount: %s. Submit final details: %s. %s-IUTCS',
                    $event,
                    $this->formattedAmount(),
                    route('final-registration.show', ['registration_code' => $code]),
                    "\n -",
                )
                : sprintf(
                    'Congratulations! Your team %s is qualified for %s at %s. Payable amount: %s. Submit final details: %s. %s-IUTCS',
                    $this->registration->team_name,
                    $event,
                    $this->formattedAmount(),
                    route('final-registration.show', ['registration_code' => $code]),
                    "\n -",
                ),
            'final_payment_confirmed' => $this->registration->participants->count() === 1
                ? sprintf(
                    'Final payment confirmed for you at %s. Check status: %s. %s-IUTCS',
                    $event,
                    route('registration.status', ['code' => $code]),
                    "\n -",
                )
                : sprintf(
                    'Final payment confirmed for your team %s at %s. Check status: %s. %s-IUTCS',
                    $this->registration->team_name,
                    $event,
                    route('registration.status', ['code' => $code]),
                    "\n -",
                ),
            'initial_payment_confirmed' => $this->registration->participants->count() === 1
                ? sprintf(
                    'Payment approved for you at %s. The T-shirt form will be shared after final intake approval. %s-IUTCS',
                    $event,
                    "\n -",
                )
                : sprintf(
                    'Payment approved for your team %s at %s. The T-shirt form will be shared after final intake approval. %s-IUTCS',
                    $this->registration->team_name,
                    $event,
                    "\n -",
                ),
            'final_intake_confirmed' => $this->registration->participants->count() === 1
                ? sprintf(
                    'Final intake approved for you at %s. Submit T-shirt details: %s. %s-IUTCS',
                    $event,
                    route('final-registration.show', ['registration_code' => $code]),
                    "\n -",
                )
                : sprintf(
                    'Final intake approved for your team %s at %s. Submit T-shirt details: %s. %s-IUTCS',
                    $this->registration->team_name,
                    $event,
                    route('final-registration.show', ['registration_code' => $code]),
                    "\n -",
                ),
            default => sprintf(
                'Thank you for your %s at %s @ IUT 12th ICT FEST 2026. Check Status at %s. %s-IUTCS',
                strtolower($this->registration->event?->isFinalRoundPaidType() ? 'Pre-Registration' : 'Registration'),
                $event,
                route('registration.status', ['code' => $code]),
                "\n -",
            ),
        };
    }

    private function formattedAmount(): string
    {
        return 'BDT '.number_format((int) ($this->registration->event?->amount ?? 0));
    }

    private function mapProviderCode(string $code): string
    {
        return match ($code) {
            '202' => 'SMS Submitted Successfully',
            '1001' => 'Invalid Number',
            '1002' => 'Sender ID not correct or sender ID is disabled',
            '1003' => 'Please required all fields or contact your system administrator',
            '1005' => 'Internal Error',
            '1006' => 'Balance validity not available',
            '1007' => 'Balance insufficient',
            '1011' => 'User Id not found',
            '1012' => 'Masking SMS must be sent in Bengali',
            '1013' => 'Sender Id has not found gateway by api key',
            '1014' => 'Sender Type Name not found using this sender by api key',
            '1015' => 'Sender Id has not found any valid gateway by api key',
            '1016' => 'Sender Type Name active price info not found by this sender id',
            '1017' => 'Sender Type Name price info not found by this sender id',
            '1018' => 'The owner of this account is disabled',
            '1019' => 'The sender type name price of this account is disabled',
            '1020' => 'The parent of this account is not found',
            '1021' => 'The parent active sender type name price of this account is not found',
            '1031' => 'Account not verified',
            '1032' => 'IP not whitelisted',
            default => 'Unknown provider response',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function parseProviderResponse(string $body): array
    {
        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : [];
    }
}
