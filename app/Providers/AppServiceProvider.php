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
                ->line('Merci pour votre inscription sur la Boutique. Cliquez ci-dessous pour activer votre compte.')
                ->action('Vérifier mon email', $url)
                ->line('Si vous n\'avez pas créé de compte, ignorez cet email.');
        });

        // ✅ $user SEULEMENT pour admin/ et client/
        View::composer(['*'], function ($view) {
            if (Auth::check()) {
                $view->with('user', Auth::user());

                // 🔥 Compteur panier pour users connectés
                $panierId = DB::table('paniers')
                    ->where('user_id', Auth::user()->id)
                    ->where('status', 'actif')
                    ->value('id');

                $cartcount = $panierId ?
                    DB::table('panier_produit')->where('paniers_id', $panierId)->sum('quantite')
                    : 0;
            } else {
                $view->with('user', null);
                $cartcount = session('cart_count', 0); // Panier invités
            }

            $view->with('cartcount', (int)$cartcount);
        });
    }
}
