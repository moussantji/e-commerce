<?php

namespace App\Notifications;

use App\Models\PromoCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Envoyé au client lorsqu'un coupon lui est attribué (cumul mensuel,
 * distribution manuelle depuis l'admin, etc.).
 *
 * Le code n'est jamais publié sur la boutique : il transite uniquement par
 * la notification interne et par email.
 */
class CouponAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public PromoCode $coupon;
    public string $source;

    public function __construct(PromoCode $coupon, string $source = 'manual')
    {
        $this->coupon = $coupon;
        $this->source = $source;
    }

    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\ExpoChannel::class, 'database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $advantage = $this->coupon->type === 'percentage'
            ? rtrim(rtrim(number_format((float) $this->coupon->value, 2, ',', ''), '0'), ',') . ' % de réduction'
            : number_format((float) $this->coupon->value, 0, ',', ' ') . ' FCFA de réduction';

        $mail = (new MailMessage())
            ->subject('🎁 Votre coupon ' . $this->coupon->code)
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line($this->reason())
            ->line('Votre code : ' . $this->coupon->code)
            ->line('Avantage : ' . $advantage)
            ->line('Aucun montant minimum n’est requis pour l’utiliser.')
            ->action('Utiliser mon coupon', url('/panier'));

        if ($this->coupon->expires_at) {
            $mail->line('Valable jusqu’au ' . $this->coupon->expires_at->format('d/m/Y à H:i') . '.');
        }

        return $mail->line('Merci pour votre confiance !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => '🎁 Nouveau coupon',
            'body' => 'Votre code ' . $this->coupon->code . ' est disponible. ' . $this->reason(),
            'icon' => 'gift-outline',
            'link' => ['type' => 'coupons'],
        ];
    }

    private function reason(): string
    {
        return match ($this->source) {
            'monthly' => 'Vos achats de ce mois vous donnent droit à un coupon fidélité.',
            'auto_order' => 'Votre commande remplit les conditions pour bénéficier de ce coupon.',
            'welcome' => 'Bienvenue ! Voici un coupon pour votre première commande.',
            default => 'Un coupon vous a été attribué.',
        };
    }
}
