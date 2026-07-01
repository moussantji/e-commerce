<?php

namespace App\Notifications;

use App\Notifications\Channels\ExpoChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification de sécurité (connexion, changement de mot de passe...).
 * base + email + push.
 */
class SecurityNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $body;
    public string $icon;
    public bool $withMail;

    public function __construct(string $title, string $body, string $icon = 'shield-checkmark-outline', bool $withMail = true)
    {
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon;
        $this->withMail = $withMail;
    }

    public function via(object $notifiable): array
    {
        $channels = [ExpoChannel::class, 'database'];
        if ($this->withMail) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->title)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line($this->body)
            ->line("Si vous n'êtes pas à l'origine de cette action, changez votre mot de passe immédiatement.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'link' => ['type' => 'none'],
        ];
    }
}
