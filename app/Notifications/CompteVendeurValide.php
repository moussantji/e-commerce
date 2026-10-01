<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompteVendeurValide extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Votre compte vendeur est activé !')
            ->greeting('Félicitations ' . ($notifiable->name ?? '') . ' !')
            ->line('Votre compte vendeur a été validé par notre équipe.')
            ->line('Connectez-vous pour ajouter vos premiers produits et recevoir des commandes.')
            ->action('Accéder à mon espace vendeur', url('/vendeur/dashboard'))
            ->line('Bonnes ventes !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Compte vendeur activé !',
            'body' => 'Ajoutez vos premiers produits.',
            'icon' => 'checkmark-done-outline',
            'link' => ['type' => 'home'],
        ];
    }
}
