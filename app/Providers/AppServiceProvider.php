<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Email de vérification en français, thème boutique.
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage())
                ->subject('Vérifiez votre adresse email')
                ->greeting('Bienvenue ' . ($notifiable->name ?? '') . ' !')
                ->line('Merci pour votre inscription sur ' . config('app.name') . '. Cliquez ci-dessous pour activer votre compte.')
                ->action('Vérifier mon email', $url)
                ->line('Si vous n\'avez pas créé de compte, ignorez cet email.');
        });

        // $user = utilisateur connecté pour les layouts admin/client.
        // Ne jamais écraser un $user passé explicitement par un contrôleur :
        // l'admin doit pouvoir consulter/modifier un autre compte que le sien.
        View::composer(['*'], function ($view) {
            $hasExplicitUser = array_key_exists('user', $view->getData());

            if (Auth::check()) {
                if (! $hasExplicitUser) {
                    $view->with('user', Auth::user());
                }

                // 🔥 Compteur panier pour users connectés
                $panierId = DB::table('paniers')
                    ->where('user_id', Auth::user()->id)
                    ->where('status', 'actif')
                    ->value('id');

                $cartcount = $panierId ?
                    DB::table('panier_produit')->where('paniers_id', $panierId)->sum('quantite')
                    : 0;
            } else {
                if (! $hasExplicitUser) {
                    $view->with('user', null);
                }
                $cartcount = session('cart_count', 0); // Panier invités
            }

            $view->with('cartcount', (int)$cartcount);
        });
    }
}
