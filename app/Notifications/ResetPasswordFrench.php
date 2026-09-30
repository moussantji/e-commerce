<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordFrench extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage())
            ->subject('Réinitialiser votre mot de passe')
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Vous recevez cet email car une réinitialisation de mot de passe a été demandée pour votre compte Boutique.')
            ->action('Réinitialiser le mot de passe', $url)
            ->line('Ce lien expire dans 60 minutes. Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.');
    }
}
