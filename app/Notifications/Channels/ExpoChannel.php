<?php

namespace App\Notifications\Channels;

use App\Support\ExpoPush;
use Illuminate\Notifications\Notification;

/**
 * Canal de notification "expo" : envoie une push sur le téléphone en se basant
 * sur toArray() de la notification (title / body / link).
 */
class ExpoChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $token = $notifiable->expo_push_token ?? null;
        if (!$token) {
            return;
        }

        $payload = method_exists($notification, 'toArray')
            ? $notification->toArray($notifiable)
            : [];

        $title = $payload['title'] ?? 'Notification';
        $body = $payload['body'] ?? ($payload['message'] ?? '');
        $data = ['link' => $payload['link'] ?? ['type' => 'none']];

        ExpoPush::send($token, $title, $body, $data);
    }
}
