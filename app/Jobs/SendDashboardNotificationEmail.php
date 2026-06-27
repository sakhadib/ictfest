<?php

namespace App\Jobs;

use App\Mail\DashboardBroadcastMail;
use App\Models\Delivery;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDashboardNotificationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 0;

    public function __construct(
        public int $deliveryId,
    ) {
        $this->onQueue('low');
    }

    public function middleware(): array
    {
        return [
            (new RateLimited('resend-emails'))->releaseAfter(1),
        ];
    }

    public function retryUntil(): \DateTimeInterface
    {
        return now()->addHours(12);
    }

    public function handle(): void
    {
        $delivery = Delivery::query()
            ->with('notification')
            ->find($this->deliveryId);

        if (! $delivery || ! $delivery->notification) {
            return;
        }

        if ($delivery->status !== 'pending') {
            return;
        }

        try {
            Mail::to($delivery->email, $delivery->name)
                ->send(new DashboardBroadcastMail(
                    $delivery->notification->subject,
                    $delivery->notification->body,
                    $delivery->name,
                ));

            $delivery->forceFill([
                'status' => 'sent',
                'error' => null,
                'sent_at' => now(),
                'failed_at' => null,
            ])->save();
        } catch (Throwable $exception) {
            $delivery->forceFill([
                'status' => 'failed',
                'error' => str($exception->getMessage())->limit(1000)->toString(),
                'failed_at' => now(),
            ])->save();

            Log::error('Dashboard notification email delivery failed.', [
                'notification_id' => $delivery->notification_id,
                'delivery_id' => $delivery->id,
                'email' => $delivery->email,
                'exception_class' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        } finally {
            $this->refreshNotificationStatus($delivery->notification);
        }
    }

    private function refreshNotificationStatus(Notification $notification): void
    {
        $counts = $notification->deliveries()
            ->selectRaw("status, count(*) as aggregate")
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $pending = (int) ($counts['pending'] ?? 0);
        $sent = (int) ($counts['sent'] ?? 0);
        $failed = (int) ($counts['failed'] ?? 0);

        if ($pending > 0) {
            $status = 'sending';
            $completedAt = null;
        } elseif ($failed > 0 && $sent > 0) {
            $status = 'partial';
            $completedAt = now();
        } elseif ($failed > 0) {
            $status = 'failed';
            $completedAt = now();
        } else {
            $status = 'sent';
            $completedAt = now();
        }

        $notification->forceFill([
            'status' => $status,
            'completed_at' => $completedAt,
        ])->save();
    }
}
