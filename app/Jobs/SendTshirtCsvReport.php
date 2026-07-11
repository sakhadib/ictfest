<?php

namespace App\Jobs;

use App\Services\TelegramBotClient;
use App\Services\TshirtCsvReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendTshirtCsvReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public function __construct(
        public string|int $chatId,
        public string $eventCode,
    ) {
    }

    public function handle(
        TshirtCsvReportService $reports,
        TelegramBotClient $telegram,
    ): void {
        $path = null;

        try {
            $report = $reports->buildForEvent($this->eventCode);

            if (! $report['ok']) {
                $telegram->sendMessage($this->chatId, $report['message'] ?? 'No T-shirt report data found.');

                return;
            }

            $path = $report['path'];
            $event = $report['event'];
            $rows = $report['rows'];
            $eventLabel = $event->code === '01' ? 'IUPC' : $event->name;
            $caption = "{$event->code} - {$eventLabel} paid T-shirt CSV ({$rows->count()} rows)";

            $response = $telegram->sendDocument($this->chatId, $path, $caption);

            Log::info('Telegram T-shirt CSV report sent.', [
                'chat_id' => $this->chatId,
                'event_code' => $event->code,
                'telegram_status' => $response->status(),
                'csv_size' => file_exists($path) ? filesize($path) : null,
                'rows' => $rows->count(),
            ]);

            if ($response->failed()) {
                Log::warning('Telegram rejected T-shirt CSV report.', [
                    'chat_id' => $this->chatId,
                    'event_code' => $event->code,
                    'telegram_status' => $response->status(),
                    'response_size' => strlen($response->body()),
                    'csv_size' => file_exists($path) ? filesize($path) : null,
                ]);

                $telegram->sendMessage($this->chatId, 'The T-shirt CSV was generated, but Telegram rejected the upload. Please try again.');
            }
        } catch (Throwable $exception) {
            Log::error('Telegram T-shirt CSV report failed.', [
                'chat_id' => $this->chatId,
                'event_code' => $this->eventCode,
                'exception_class' => $exception::class,
            ]);

            $telegram->sendMessage($this->chatId, 'Could not generate the T-shirt CSV right now. Please try again later.');
        } finally {
            if ($path && file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
