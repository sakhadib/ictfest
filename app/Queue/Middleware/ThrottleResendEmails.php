<?php

namespace App\Queue\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class ThrottleResendEmails
{
    private const LOCK_KEY = 'resend-email-send-lock';

    private const LAST_SEND_KEY = 'resend-email-last-send-microtime';

    /**
     * Keep below Resend's 2 req/sec limit to avoid edge bursts across workers.
     */
    private const MINIMUM_SPACING_MICROSECONDS = 700000;

    public function handle(object $job, Closure $next): mixed
    {
        Cache::lock(self::LOCK_KEY, 10)->block(10, function (): void {
            $lastSendAt = (float) Cache::get(self::LAST_SEND_KEY, 0);
            $nextAllowedAt = $lastSendAt + (self::MINIMUM_SPACING_MICROSECONDS / 1000000);
            $now = microtime(true);

            if ($nextAllowedAt > $now) {
                usleep((int) (($nextAllowedAt - $now) * 1000000));
            }

            Cache::put(self::LAST_SEND_KEY, microtime(true), now()->addMinutes(10));
        });

        return $next($job);
    }
}
