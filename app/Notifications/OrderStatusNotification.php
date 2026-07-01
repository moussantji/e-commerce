<?php

namespace App\Notifications;

use App\Models\Commandes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification (base + email) envoyée au client à chaque changement de
 * statut de sa commande.
 */
class OrderStatusNotification extends Notification
{
    use Queueable;

    public Commandes $order;
    public string $statusLabel;

    public function __construct(Commandes $order, string $statusLabel)
    {
        $this->order = $order;
        $this->statusLabel = $statusLabel;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $numero = $this->order->numero_commande ?? ('#' . $this->order->id);
        $total = number_format((float) $this->order->total, 0, ',', ' ');

        return (new MailMessage())
            ->subject("Commande {$numero} : {$this->statusLabel}")
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line("Le statut de votre commande {$numero} est maintenant : {$this->statusLabel}.")
            ->line("Montant : {$total} FCFA")
            ->action('Voir ma commande', url('/commande/' . $this->order->id))
            ->line('Merci pour votre confiance !');
    }

    public function toArray(object $notifiable): array
    {
        $numero = $this->order->numero_commande ?? ('#' . $this->order->id);

        return [
            'title' => "Commande {$numero}",
            'body' => "Nouveau statut : {$this->statusLabel}.",
            'icon' => 'cube-outline',
            'link' => ['type' => 'order', 'id' => $this->order->id],
        ];
    }
}
