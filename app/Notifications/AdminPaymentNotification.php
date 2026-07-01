<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification (base + email) envoyée aux ADMINS lorsqu'un client déclare
 * avoir payé (commande ou rechargement de portefeuille).
 */
class AdminPaymentNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $body;
    public array $link;

    /**
     * @param string $title  Titre court
     * @param string $body   Détail
     * @param array  $link   Cible in-app : ['type' => 'admin_payment'|'admin_wallet', 'id' => x]
     */
    public function __construct(string $title, string $body, array $link = ['type' => 'none'])
    {
        $this->title = $title;
        $this->body = $body;
        $this->link = $link;
    }

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\ExpoChannel::class, 'database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('💳 ' . $this->title)
            ->greeting('Bonjour ' . ($notifiable->name ?? 'Admin'))
            ->line($this->body)
            ->action('Ouvrir le tableau de bord', url('/dashboard'))
            ->line('Merci de vérifier et de confirmer ce paiement.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => 'cash-outline',
            'link' => $this->link,
        ];
    }
}
