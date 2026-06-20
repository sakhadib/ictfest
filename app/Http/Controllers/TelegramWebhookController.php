<?php

namespace App\Http\Controllers;

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

        $reply = $this->replyFor($text, $summary);

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

    private function replyFor(string $text, RegistrationSummaryService $summary): ?string
    {
        $command = $this->normalizedCommand($text);

        if ($command === '') {
            return null;
        }

        if (preg_match('/^\/?event\s+([0-9]{1,2})$/', $command, $matches)) {
            return $summary->eventText($matches[1]);
        }

        return match ($command) {
            'help', 'commands', '/help', '/commands' => $summary->helpText(),
            'status', '/status' => $summary->telegramText(),
            'today', '/today' => $summary->todayText(),
            'events', '/events', 'live', '/live' => $summary->eventsText(),
            'pending', '/pending' => $summary->pendingText(),
            'payments', '/payments' => $summary->paymentsText(),
            'finals', '/finals' => $summary->finalsText(),
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
        $text = preg_replace('/\s+/', ' ', $text) ?? '';

        return preg_replace('/^\/([a-z]+)@[a-z0-9_]+/i', '/$1', $text) ?? '';
    }
}
