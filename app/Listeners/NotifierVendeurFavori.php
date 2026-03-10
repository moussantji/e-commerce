<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\ProductFavoriAjoute;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\ProductFavoriNotification;

class NotifierVendeurFavori
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductFavoriAjoute $event)
    {
        // ✅ Récupère vendeur via relation ou table produits
        $vendeur = User::find($event->product->user_id ?? $event->product->vendeur_id);

        if ($vendeur && $vendeur->id != auth()->id()) {
            $vendeur->notify(new ProductFavoriNotification($event->user, $event->product));
        }
    }
}
