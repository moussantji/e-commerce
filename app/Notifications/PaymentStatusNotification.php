<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification (base + email) envoyée au CLIENT quand l'admin confirme ou
 * rejette son paiement / rechargement.
 */
class PaymentStatusNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $body;
    public array $link;
    public bool $confirmed;

    public function __construct(string $title, string $body, bool $confirmed, array $link = ['type' => 'none'])
    {
        $this->title = $title;
        $this->body = $body;
        $this->confirmed = $confirmed;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\ExpoChannel::class, 'database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject(($this->confirmed ? '✅ ' : '⚠️ ') . $this->title)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line($this->body);

        if ($this->confirmed) {
            $mail->action('Voir mon compte', url('/'));
        }

        return $mail->line('Merci pour votre confiance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->confirmed ? 'checkmark-circle-outline' : 'alert-circle-outline',
            'link' => $this->link,
        ];
    }
}
