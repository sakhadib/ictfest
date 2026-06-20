<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

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

    private function endpoint(string $method): string
    {
        $apiUrl = rtrim((string) config('services.telegram.api_url', 'https://api.telegram.org'), '/');
        $botToken = (string) config('services.telegram.bot_token');

        return $apiUrl.'/bot'.$botToken.'/'.$method;
    }
}
