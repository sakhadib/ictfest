<?php

namespace App\Jobs;

use App\Services\RegistrationCardPdfService;
use App\Services\RegistrationCardService;
use App\Services\TelegramBotClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendRegistrationCards implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(
        public string|int $chatId,
        public string $mode,
        public ?string $value = null,
    ) {
    }

    public function handle(
        RegistrationCardService $cards,
        RegistrationCardPdfService $pdfs,
        TelegramBotClient $telegram,
    ): void {
        $paths = [];

        try {
            match ($this->mode) {
                'force' => $paths[] = $this->sendReport($telegram, $pdfs, $cards->all(), 'All Registration Cards', 'ictfest-regcards-all-'),
                'event' => $paths[] = $this->sendReport($telegram, $pdfs, $cards->event((string) $this->value), 'Registration Cards - Event '.str_pad((string) $this->value, 2, '0', STR_PAD_LEFT), 'ictfest-regcards-event-'),
                'team' => $paths[] = $this->sendReport($telegram, $pdfs, $cards->team((string) $this->value), 'Registration Card - '.strtoupper((string) $this->value), 'ictfest-regcard-team-'),
                'all' => $this->sendAllEventReports($cards, $pdfs, $telegram, $paths),
                default => $telegram->sendMessage($this->chatId, 'Invalid registration card command. Use /regcard all, /regcard force, /regcard event 01, or /regcard team REG-CODE.'),
            };
        } catch (Throwable $exception) {
            Log::error('Telegram registration cards failed.', [
                'chat_id' => $this->chatId,
                'mode' => $this->mode,
                'value' => $this->value,
                'exception_class' => $exception::class,
            ]);

            $telegram->sendMessage($this->chatId, 'Could not generate registration cards right now. Please try again later.');
        } finally {
            foreach ($paths as $path) {
                if ($path && file_exists($path)) {
                    @unlink($path);
                }
            }
        }
    }

    /**
     * @param list<string|null> $paths
     */
    private function sendAllEventReports(
        RegistrationCardService $cards,
        RegistrationCardPdfService $pdfs,
        TelegramBotClient $telegram,
        array &$paths,
    ): void {
        $telegram->sendMessage($this->chatId, 'Generating registration cards by event. Each event will arrive as a separate PDF.');

        foreach ($cards->eventsWithRegistrations() as $event) {
            $paths[] = $this->sendReport(
                telegram: $telegram,
                pdfs: $pdfs,
                report: $cards->event($event->code),
                caption: $event->code.' - '.$event->name.' Registration Cards',
                prefix: 'ictfest-regcards-'.$event->code.'-',
            );
        }

        $telegram->sendMessage($this->chatId, 'Registration cards sent.');
    }

    /**
     * @param array<string, mixed> $report
     */
    private function sendReport(
        TelegramBotClient $telegram,
        RegistrationCardPdfService $pdfs,
        array $report,
        string $caption,
        string $prefix,
    ): ?string {
        if (! $report['ok']) {
            $telegram->sendMessage($this->chatId, $report['message']);

            return null;
        }

        $path = $pdfs->renderToTempFile($report, $prefix);

        try {
            $response = $telegram->sendDocument($this->chatId, $path, $caption);

            Log::info('Telegram registration cards sent.', [
                'chat_id' => $this->chatId,
                'caption' => $caption,
                'telegram_status' => $response->status(),
                'pdf_size' => file_exists($path) ? filesize($path) : null,
                'cards' => $report['registrations']->count(),
            ]);

            if ($response->failed()) {
                Log::warning('Telegram rejected registration cards PDF.', [
                    'chat_id' => $this->chatId,
                    'caption' => $caption,
                    'telegram_status' => $response->status(),
                    'response_size' => strlen($response->body()),
                    'pdf_size' => file_exists($path) ? filesize($path) : null,
                ]);

                $telegram->sendMessage($this->chatId, 'Telegram refused this registration cards PDF. Try /regcard all to receive smaller event-wise files.');
            }

            return $path;
        } catch (Throwable $exception) {
            if (file_exists($path)) {
                @unlink($path);
            }

            throw $exception;
        }
    }
}
