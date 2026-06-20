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

        if (! $chatId || ! $this->isAllowedChat($chatId) || ! $this->isStatusCommand($text)) {
            return response()->json(['ok' => true]);
        }

        $response = $telegram->sendMessage($chatId, $summary->telegramText());

        Log::info('Telegram webhook status command response sent.', [
            'chat_id' => $chatId,
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

    private function isStatusCommand(string $text): bool
    {
        $text = strtolower(trim($text));

        return $text === 'status' || $text === '/status' || str_starts_with($text, '/status@');
    }

    private function isAllowedChat(mixed $chatId): bool
    {
        $allowedChatId = (string) config('services.telegram.chat_id');

        return $allowedChatId === '' || (string) $chatId === $allowedChatId;
    }
}
