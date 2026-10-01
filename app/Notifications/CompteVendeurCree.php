<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompteVendeurCree extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Compte vendeur reçu — en cours de validation')
            ->greeting('Bonjour ' . ($notifiable->name ?? '') . ' !')
            ->line('Votre demande de compte vendeur a bien été reçue.')
            ->line('Notre équipe la vérifie actuellement. Vous recevrez un email dès son activation.')
            ->line('En attendant, finalisez votre demande sur WhatsApp pour accélérer la validation.')
            ->action('Continuer sur WhatsApp', 'https://wa.me/' . \App\Support\WhatsApp::number())
            ->line('Merci pour votre confiance !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Compte vendeur en validation',
            'body' => 'Votre demande est en cours de vérification.',
            'icon' => 'store-outline',
            'link' => ['type' => 'home'],
        ];
    }
}
