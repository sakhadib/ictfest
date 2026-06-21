<?php

namespace App\Jobs;

use App\Services\QuickChartService;
use App\Services\RegistrationTrendService;
use App\Services\TelegramBotClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendRegistrationTrendChart implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public function __construct(
        public string|int $chatId,
        public string $scope,
    ) {
    }

    public function handle(
        RegistrationTrendService $trends,
        QuickChartService $charts,
        TelegramBotClient $telegram,
    ): void {
        $trend = $this->scope === 'all'
            ? $trends->allEvents()
            : $trends->singleEvent($this->scope);

        if (! $trend['ok']) {
            $telegram->sendMessage($this->chatId, $trend['message']);

            return;
        }

        $path = null;

        try {
            $path = $charts->lineChartPng(
                labels: $trend['labels'],
                datasets: $trend['datasets'],
                title: $trend['title'],
            );

            $telegram->sendPhoto($this->chatId, $path, $trend['title']);
        } catch (Throwable $exception) {
            Log::error('Telegram registration trend chart failed.', [
                'scope' => $this->scope,
                'chat_id' => $this->chatId,
                'exception' => $exception->getMessage(),
            ]);

            $telegram->sendMessage($this->chatId, 'Could not generate the registration trend chart right now. Please try again later.');
        } finally {
            if ($path && file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
