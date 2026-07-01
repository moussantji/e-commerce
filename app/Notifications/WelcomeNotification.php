<?php

namespace App\Notifications;

use App\Notifications\Channels\ExpoChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification (base + email + push) de bienvenue à l'inscription.
 */
class WelcomeNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [ExpoChannel::class, 'database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Bienvenue 🎉')
            ->greeting('Bonjour ' . ($notifiable->name ?? '') . ' 👋')
            ->line('Votre compte a été créé avec succès. Bienvenue dans notre boutique !')
            ->line('Profitez de la livraison offerte dès 25 000 FCFA sur votre première commande.')
            ->action('Découvrir la boutique', url('/'))
            ->line('Bonne découverte !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Bienvenue 🎉',
            'body' => 'Votre compte a été créé. Bonne découverte !',
            'icon' => 'gift-outline',
            'link' => ['type' => 'home'],
        ];
    }
}
