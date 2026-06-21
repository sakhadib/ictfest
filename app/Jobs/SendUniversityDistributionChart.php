<?php

namespace App\Jobs;

use App\Services\QuickChartService;
use App\Services\TelegramBotClient;
use App\Services\UniversityDistributionService;
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
        QuickChartService $charts,
        TelegramBotClient $telegram,
    ): void {
        $chart = $distribution->participantCounts();

        if (! $chart['ok']) {
            $telegram->sendMessage($this->chatId, $chart['message']);

            return;
        }

        $path = null;

        try {
            $path = $charts->barChartPng(
                labels: $chart['labels'],
                data: $chart['data'],
                title: $chart['title'],
                datasetLabel: 'Participant Count',
            );

            $response = $telegram->sendPhoto($this->chatId, $path, $chart['title']);

            if ($response->failed()) {
                Log::warning('Telegram rejected university distribution chart.', [
                    'chat_id' => $this->chatId,
                    'telegram_status' => $response->status(),
                    'response_size' => strlen($response->body()),
                    'chart_size' => file_exists($path) ? filesize($path) : null,
                ]);

                $telegram->sendMessage($this->chatId, 'The university distribution chart was generated, but Telegram rejected the upload. Please try again.');

                return;
            }

            Log::info('Telegram university distribution chart sent.', [
                'chat_id' => $this->chatId,
                'telegram_status' => $response->status(),
                'chart_size' => file_exists($path) ? filesize($path) : null,
            ]);
        } catch (Throwable $exception) {
            Log::error('Telegram university distribution chart failed.', [
                'chat_id' => $this->chatId,
                'exception_class' => $exception::class,
            ]);

            $telegram->sendMessage($this->chatId, 'Could not generate the university distribution chart right now. Please try again later.');
        } finally {
            if ($path && file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
