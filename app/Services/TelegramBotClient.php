<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramBotClient
{
    public function sendMessage(string|int $chatId, string $text): Response
    {
        return Http::timeout(20)->asForm()->post($this->endpoint('sendMessage'), [
            'chat_id' => $chatId,
            'text' => $text,
            'disable_web_page_preview' => true,
        ]);
    }

    public function sendPhoto(string|int $chatId, string $photoPath, ?string $caption = null): Response
    {
        $handle = fopen($photoPath, 'r');

        if (! $handle) {
            throw new RuntimeException('Could not open Telegram photo file.');
        }

        try {
            $request = Http::timeout(30)
                ->attach('photo', $handle, basename($photoPath));

            return $request->post($this->endpoint('sendPhoto'), array_filter([
                'chat_id' => $chatId,
                'caption' => $caption,
            ], fn ($value) => $value !== null && $value !== ''));
        } finally {
            fclose($handle);
        }
    }

    private function endpoint(string $method): string
    {
        $apiUrl = rtrim((string) config('services.telegram.api_url', 'https://api.telegram.org'), '/');
        $botToken = (string) config('services.telegram.bot_token');

        return $apiUrl.'/bot'.$botToken.'/'.$method;
    }
}
