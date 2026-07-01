<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envoi de notifications push via l'API Expo (https://exp.host).
 */
class ExpoPush
{
    public static function send($tokens, string $title, string $body, array $data = []): void
    {
        $tokens = collect(is_array($tokens) ? $tokens : [$tokens])
            ->filter(fn ($t) => is_string($t) && str_starts_with($t, 'ExponentPushToken'))
            ->values();

        if ($tokens->isEmpty()) {
            return;
        }

        $messages = $tokens->map(fn ($token) => [
            'to' => $token,
            'title' => $title,
            'body' => $body,
            'sound' => 'default',
            'data' => $data,
            'channelId' => 'default',
        ])->all();

        try {
            Http::acceptJson()
                ->timeout(10)
                ->post('https://exp.host/--/api/v2/push/send', $messages);
        } catch (\Throwable $e) {
            Log::warning('Expo push échouée : ' . $e->getMessage());
        }
    }
}
