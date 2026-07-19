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

    /**
     * @return list<Response>
     */
    public function sendTextChunks(string|int $chatId, string $text, int $limit = 3900): array
    {
        $lines = preg_split("/\r\n|\n|\r/", $text) ?: [$text];
        $chunks = [];
        $current = '';

        foreach ($lines as $line) {
            $candidate = $current === '' ? $line : $current."\n".$line;

            if (mb_strlen($candidate, 'UTF-8') <= $limit) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
                $current = '';
            }

            while (mb_strlen($line, 'UTF-8') > $limit) {
                $chunks[] = mb_substr($line, 0, $limit, 'UTF-8');
                $line = mb_substr($line, $limit, null, 'UTF-8');
            }

            $current = $line;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return array_map(fn (string $chunk): Response => $this->sendMessage($chatId, $chunk), $chunks);
    }

    public function sendPhoto(string|int $chatId, string $photoPath, ?string $caption = null): Response
    {
        $handle = fopen($photoPath, 'rb');

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

    public function sendDocument(string|int $chatId, string $documentPath, ?string $caption = null): Response
    {
        $handle = fopen($documentPath, 'rb');

        if (! $handle) {
            throw new RuntimeException('Could not open Telegram document file.');
        }

        try {
            $request = Http::timeout(30)
                ->attach('document', $handle, basename($documentPath));

            return $request->post($this->endpoint('sendDocument'), array_filter([
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
