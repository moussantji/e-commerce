<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Produits;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProductFavoriNotification extends Notification
{
    use Queueable;

    public $user;
    public $product;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Produits $product)
    {
        $this->user = $user;
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'user_name' => $this->user->name,
            'message' => "{$this->user->name} a ajouté {$this->product->name} en favoris ❤️",
            'icon' => '❤️',
            'url' => route('produits.show', ['slug' => $this->product->getSlug(), 'id' => $this->product->id]),
            'product_title' => $this->product->name
        ];
    }
}
