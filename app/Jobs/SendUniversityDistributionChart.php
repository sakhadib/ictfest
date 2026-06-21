<?php

namespace App\Jobs;

use App\Services\TelegramBotClient;
use App\Services\UniversityDistributionService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendUniversityDistributionChart implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public function __construct(
        public string|int $chatId,
    ) {
    }

    public function handle(
        UniversityDistributionService $distribution,
        TelegramBotClient $telegram,
    ): void {
        $report = $distribution->participantCountTable();

        if (! $report['ok']) {
            $telegram->sendMessage($this->chatId, $report['message']);

            return;
        }

        $path = null;

        try {
            $path = $this->generatePdf($report);

            $response = $telegram->sendDocument($this->chatId, $path, $report['title']);

            if ($response->failed()) {
                Log::warning('Telegram rejected university distribution report.', [
                    'chat_id' => $this->chatId,
                    'telegram_status' => $response->status(),
                    'response_size' => strlen($response->body()),
                    'pdf_size' => file_exists($path) ? filesize($path) : null,
                ]);

                $telegram->sendMessage($this->chatId, 'The university distribution PDF was generated, but Telegram rejected the upload. Please try again.');

                return;
            }

            Log::info('Telegram university distribution report sent.', [
                'chat_id' => $this->chatId,
                'telegram_status' => $response->status(),
                'pdf_size' => file_exists($path) ? filesize($path) : null,
            ]);
        } catch (Throwable $exception) {
            Log::error('Telegram university distribution report failed.', [
                'chat_id' => $this->chatId,
                'exception_class' => $exception::class,
            ]);

            $telegram->sendMessage($this->chatId, 'Could not generate the university distribution PDF right now. Please try again later.');
        } finally {
            if ($path && file_exists($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * @param array<string, mixed> $report
     */
    private function generatePdf(array $report): string
    {
        $html = view('telegram.university-distribution-pdf', [
            'report' => $report,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        $path = tempnam(sys_get_temp_dir(), 'ictfest-univ-');

        if (! $path) {
            throw new \RuntimeException('Could not create a temporary university report file.');
        }

        $pdfPath = $path.'.pdf';
        rename($path, $pdfPath);

        $bytesWritten = file_put_contents($pdfPath, $dompdf->output());

        if ($bytesWritten === false) {
            throw new \RuntimeException('Could not write the temporary university report file.');
        }

        Log::info('University distribution PDF generated', [
            'rows' => $report['rows']->count(),
            'size' => $bytesWritten,
            'path' => basename($pdfPath),
        ]);

        return $pdfPath;
    }
}
