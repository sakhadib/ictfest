<?php

namespace App\Jobs;

use App\Models\IupcCoachLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendIupcCoachPortalSms implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $coachLinkId,
        public string $url,
    ) {
    }

    public function handle(): void
    {
        $coachLink = IupcCoachLink::query()->with(['coach', 'allocation'])->find($this->coachLinkId);

        if (! $coachLink || ! $coachLink->isActive()) {
            return;
        }

        $apiKey = (string) config('services.bulk_sms.api_key');
        $senderId = (string) config('services.bulk_sms.sender_id');
        $apiUrl = (string) config('services.bulk_sms.url');
        $number = $this->normalizePhoneNumber($coachLink->coach?->contact_number);

        if (! $apiKey || ! $senderId || ! $apiUrl || ! $number) {
            Log::warning('IUPC coach portal SMS skipped.', [
                'coach_link_id' => $coachLink->id,
                'coach_id' => $coachLink->iupc_coach_contact_id,
                'phone' => $coachLink->coach?->contact_number,
                'normalized_phone' => $number,
            ]);

            return;
        }

        $message = sprintf(
            'IUPC final registration link for %s: %s -IUTCS',
            $coachLink->allocation?->name ?? 'your university',
            $this->url,
        );

        $response = Http::timeout(20)->asForm()->post($apiUrl, [
            'api_key' => $apiKey,
            'number' => $number,
            'senderid' => $senderId,
            'message' => $message,
        ]);

        Log::info('IUPC coach portal SMS API response.', [
            'coach_link_id' => $coachLink->id,
            'phone' => $number,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $coachLink->update([
            'last_sms_sent_at' => now(),
            'last_sent_at' => now(),
        ]);
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
}
