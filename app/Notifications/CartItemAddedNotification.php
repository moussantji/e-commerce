<?php

namespace App\Notifications;

use App\Models\Produits;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification (base de données + email) envoyée à l'utilisateur
 * lorsqu'il ajoute un produit à son panier.
 */
class CartItemAddedNotification extends Notification
{
    use Queueable;

    public Produits $product;
    public int $quantity;

    public function __construct(Produits $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $prix = number_format((float) ($this->product->sale_price ?? $this->product->price), 0, ',', ' ');

        return (new MailMessage())
            ->subject('🛒 Article ajouté à votre panier')
            ->greeting('Bonjour ' . ($notifiable->name ?? '') . ' 👋')
            ->line("Vous venez d'ajouter {$this->quantity} x « {$this->product->name} » à votre panier.")
            ->line("Prix unitaire : {$prix} FCFA")
            ->action('Voir mon panier', url('/panier'))
            ->line('Finalisez votre commande avant que le stock ne parte !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Ajouté au panier 🛒',
            'body' => "{$this->quantity} x {$this->product->name} ajouté à votre panier.",
            'icon' => 'cart-outline',
            'product_id' => $this->product->id,
        ];
    }
}
