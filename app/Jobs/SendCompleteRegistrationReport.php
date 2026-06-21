<?php

namespace App\Jobs;

use App\Services\CompleteRegistrationReportService;
use App\Services\CompleteReportPdfService;
use App\Services\TelegramBotClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendCompleteRegistrationReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(
        public string|int $chatId,
        public bool $forceSingleFile = false,
    ) {
    }

    public function handle(
        CompleteRegistrationReportService $reports,
        CompleteReportPdfService $pdfs,
        TelegramBotClient $telegram,
    ): void {
        $paths = [];

        try {
            if ($this->forceSingleFile) {
                $this->sendForcedSingleReport($reports, $pdfs, $telegram);

                return;
            }

            $telegram->sendMessage($this->chatId, 'Generating complete registration PDF report. This may arrive in multiple parts.');

            $overviewReport = $reports->build(includeDetails: false);
            $paths[] = $this->sendReportPart(
                telegram: $telegram,
                pdfs: $pdfs,
                report: $overviewReport,
                caption: $overviewReport['title'].' - Overview',
                prefix: 'ictfest-full-overview-',
            );

            foreach ($reports->events() as $event) {
                $eventReport = $reports->build(eventCode: $event->code, includeDetails: true);

                if ((int) $eventReport['totals']['registrations'] === 0) {
                    continue;
                }

                $paths[] = $this->sendReportPart(
                    telegram: $telegram,
                    pdfs: $pdfs,
                    report: $eventReport,
                    caption: $event->code.' - '.$event->name,
                    prefix: 'ictfest-full-'.$event->code.'-',
                );
            }

            $telegram->sendMessage($this->chatId, 'Complete registration PDF report sent.');
        } catch (Throwable $exception) {
            Log::error('Telegram complete registration report failed.', [
                'chat_id' => $this->chatId,
                'exception_class' => $exception::class,
            ]);

            $telegram->sendMessage($this->chatId, 'Could not generate the complete registration PDF report right now. Please try again later.');
        } finally {
            foreach ($paths as $path) {
                if ($path && file_exists($path)) {
                    @unlink($path);
                }
            }
        }
    }

    private function sendForcedSingleReport(
        CompleteRegistrationReportService $reports,
        CompleteReportPdfService $pdfs,
        TelegramBotClient $telegram,
    ): void {
        $path = null;

        try {
            $telegram->sendMessage($this->chatId, 'Generating one complete PDF report. Telegram may reject it if the file is too large.');

            $report = $reports->build(includeDetails: true);
            $path = $pdfs->renderToTempFile($report, 'ictfest-full-forced-');
            $response = $telegram->sendDocument($this->chatId, $path, $report['title'].' - Complete Single PDF');

            Log::info('Telegram forced complete registration report sent.', [
                'chat_id' => $this->chatId,
                'telegram_status' => $response->status(),
                'pdf_size' => file_exists($path) ? filesize($path) : null,
                'registrations' => $report['totals']['registrations'] ?? null,
            ]);

            if ($response->failed()) {
                Log::warning('Telegram rejected forced complete registration report.', [
                    'chat_id' => $this->chatId,
                    'telegram_status' => $response->status(),
                    'response_size' => strlen($response->body()),
                    'pdf_size' => file_exists($path) ? filesize($path) : null,
                ]);

                $telegram->sendMessage(
                    $this->chatId,
                    'Telegram refused the single full report PDF. Please use /fullreport for split Telegram delivery, or download the full PDF from the admin panel: '.route('dashboard.reports.complete-pdf')
                );

                return;
            }

            $telegram->sendMessage($this->chatId, 'Single complete registration PDF report sent.');
        } catch (Throwable $exception) {
            Log::error('Telegram forced complete registration report failed.', [
                'chat_id' => $this->chatId,
                'exception_class' => $exception::class,
            ]);

            $telegram->sendMessage(
                $this->chatId,
                'Could not generate or send the single full report PDF. Please use /fullreport for split Telegram delivery, or download it from the admin panel: '.route('dashboard.reports.complete-pdf')
            );
        } finally {
            if ($path && file_exists($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * @param array<string, mixed> $report
     */
    private function sendReportPart(
        TelegramBotClient $telegram,
        CompleteReportPdfService $pdfs,
        array $report,
        string $caption,
        string $prefix,
    ): string {
        $path = $pdfs->renderToTempFile($report, $prefix);

        try {
            $response = $telegram->sendDocument($this->chatId, $path, $caption);

            Log::info('Telegram complete registration report part sent.', [
                'chat_id' => $this->chatId,
                'caption' => $caption,
                'telegram_status' => $response->status(),
                'pdf_size' => file_exists($path) ? filesize($path) : null,
                'registrations' => $report['totals']['registrations'] ?? null,
            ]);

            if ($response->failed()) {
                Log::warning('Telegram rejected complete registration report part.', [
                    'chat_id' => $this->chatId,
                    'caption' => $caption,
                    'telegram_status' => $response->status(),
                    'response_size' => strlen($response->body()),
                    'pdf_size' => file_exists($path) ? filesize($path) : null,
                ]);

                throw new \RuntimeException('Telegram rejected complete registration report part.');
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
