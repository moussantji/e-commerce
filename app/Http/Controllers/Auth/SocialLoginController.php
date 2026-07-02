<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        // Échange du code OAuth (peut échouer : réseau, state, secret…)
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect('/login')->with(
                'error',
                "La connexion via {$provider} a échoué. Merci de réessayer.",
            );
        }

        $email = $socialUser->getEmail();
        if (! $email) {
            return redirect('/login')->with(
                'error',
                "Impossible de récupérer votre e-mail depuis {$provider}.",
            );
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            // Utilisateur existant : on lie juste le provider (sans toucher au mot de passe)
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ])->save();
        } else {
            // Nouveau compte social : mot de passe aléatoire (colonne NON nullable +
            // cast 'hashed' → il est haché automatiquement ; il n'est jamais utilisé
            // puisque la connexion se fait via le provider).
            $user = User::create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Utilisateur',
                'email' => $email,
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => Str::random(40),
                'role' => 'customer',
            ]);
        }

        Auth::login($user, true);

        return redirect('/dashboard');
    }
}
