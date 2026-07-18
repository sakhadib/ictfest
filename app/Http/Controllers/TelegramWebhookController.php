<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegistrationTrendChart;
use App\Jobs\SendCompleteRegistrationReport;
use App\Jobs\SendRegistrationCards;
use App\Jobs\SendUniversityDistributionChart;
use App\Jobs\SendTshirtCsvReport;
use App\Services\PersonFastFindService;
use App\Services\RegistrationSummaryService;
use App\Services\TelegramBotClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        RegistrationSummaryService $summary,
        PersonFastFindService $people,
        TelegramBotClient $telegram,
    ): JsonResponse {
        if (! $this->hasValidSecret($request)) {
            Log::warning('Telegram webhook rejected because secret token did not match.');

            return response()->json(['ok' => false], 403);
        }

        $message = $request->input('message') ?? $request->input('edited_message');

        if (! is_array($message)) {
            return response()->json(['ok' => true]);
        }

        if ((bool) data_get($message, 'from.is_bot')) {
            return response()->json(['ok' => true]);
        }

        $chatId = data_get($message, 'chat.id');
        $text = trim((string) data_get($message, 'text', ''));

        if (! $chatId || ! $this->isAllowedChat($chatId)) {
            return response()->json(['ok' => true]);
        }

        $reply = $this->replyFor($text, $summary, $people, $chatId);

        if ($reply === '__job_dispatched__') {
            return response()->json(['ok' => true]);
        }

        if (! $reply) {
            return response()->json(['ok' => true]);
        }

        $response = $telegram->sendMessage($chatId, $reply);

        Log::info('Telegram webhook command response sent.', [
            'chat_id' => $chatId,
            'command' => $this->normalizedCommand($text),
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return response()->json(['ok' => true]);
    }

    private function hasValidSecret(Request $request): bool
    {
        $secret = (string) config('services.telegram.webhook_secret');

        if ($secret === '') {
            return true;
        }

        return hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'));
    }

    private function replyFor(string $text, RegistrationSummaryService $summary, PersonFastFindService $people, string|int $chatId): ?string
    {
        $command = $this->normalizedCommand($text);

        if ($command === '') {
            return null;
        }

        if (preg_match('/^\/?event\s+([0-9]{1,2})$/', $command, $matches)) {
            return $summary->eventText($matches[1]);
        }

        if (preg_match('/^\/?who\s+(.+)$/', $command, $matches)) {
            return $people->telegramText($matches[1]);
        }

        if (in_array($command, ['who', '/who'], true)) {
            return $people->telegramText('');
        }

        if (preg_match('/^\/?trend\s+(all|[0-9]{1,2})$/', $command, $matches)) {
            SendRegistrationTrendChart::dispatch($chatId, $matches[1]);

            return '__job_dispatched__';
        }

        if (preg_match('/^\/?regcard\s+force$/', $command)) {
            SendRegistrationCards::dispatch($chatId, 'force');

            return '__job_dispatched__';
        }

        if (preg_match('/^\/?regcard\s+all$/', $command)) {
            SendRegistrationCards::dispatch($chatId, 'all');

            return '__job_dispatched__';
        }

        if (preg_match('/^\/?regcard\s+event\s+([0-9]{1,2})\s+all$/', $command, $matches)) {
            SendRegistrationCards::dispatch($chatId, 'event_all', $matches[1]);

            return '__job_dispatched__';
        }

        if (preg_match('/^\/?regcard\s+event\s+([0-9]{1,2})$/', $command, $matches)) {
            SendRegistrationCards::dispatch($chatId, 'event', $matches[1]);

            return '__job_dispatched__';
        }

        if (preg_match('/^\/?regcard\s+team\s+([a-z0-9_-]+)$/', $command, $matches)) {
            SendRegistrationCards::dispatch($chatId, 'team', $matches[1]);

            return '__job_dispatched__';
        }

        if (preg_match('/^\/?tshirt\s+event\s+([0-9]{1,2})$/', $command, $matches)) {
            SendTshirtCsvReport::dispatch($chatId, $matches[1]);

            return '__job_dispatched__';
        }

        if (in_array($command, ['univ', '/univ'], true)) {
            SendUniversityDistributionChart::dispatch($chatId);

            return '__job_dispatched__';
        }

        if (in_array($command, ['fullreport', '/fullreport'], true)) {
            SendCompleteRegistrationReport::dispatch($chatId);

            return '__job_dispatched__';
        }

        if (in_array($command, ['fullreport-force', '/fullreport-force'], true)) {
            SendCompleteRegistrationReport::dispatch($chatId, true);

            return '__job_dispatched__';
        }

        return match ($command) {
            'help', 'commands', '/help', '/commands' => $summary->helpText(),
            'status', '/status' => $summary->telegramText(),
            'today', '/today' => $summary->todayText(),
            'events', '/events', 'live', '/live' => $summary->eventsText(),
            'pending', '/pending' => $summary->pendingText(),
            'payments', '/payments' => $summary->paymentsText(),
            'finals', '/finals' => $summary->finalsText(),
            'ca', '/ca' => $summary->caText(),
            default => null,
        };
    }

    private function isAllowedChat(mixed $chatId): bool
    {
        $allowedChatId = (string) config('services.telegram.chat_id');

        return $allowedChatId === '' || (string) $chatId === $allowedChatId;
    }

    private function normalizedCommand(string $text): string
    {
        $text = strtolower(trim($text));
        $text = str_starts_with($text, '\\') ? '/'.ltrim($text, '\\') : $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? '';

        return preg_replace('/^\/([a-z0-9_-]+)@[a-z0-9_]+/i', '/$1', $text) ?? '';
    }
}
