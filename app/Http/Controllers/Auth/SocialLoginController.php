<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(Request $request, $provider)
    {
        abort_unless($provider === 'google', 404);

        if ($request->boolean('mobile')) {
            // Le retour se fait vers l'app (Expo Go : exp://…, build : ecommerce://…).
            // On le garde en session le temps du passage chez Google.
            session([
                'social_mobile' => true,
                'social_return_to' => $this->safeReturnTo($request->query('return_to')),
            ]);
        } else {
            session()->forget(['social_mobile', 'social_return_to']);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        // Échange du code OAuth (peut échouer : réseau, state, secret…)
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            if ($this->isMobileFlow()) {
                return $this->mobileRedirect('error=google');
            }

            return redirect('/login')->with(
                'error',
                "La connexion via {$provider} a échoué. Merci de réessayer.",
            );
        }

        $email = $socialUser->getEmail();
        if (! $email) {
            if ($this->isMobileFlow()) {
                return $this->mobileRedirect('error=email');
            }

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
                'status' => 'active',
            ]);
        }

        Auth::login($user, true);

        // App mobile : on échange un code court contre un token Sanctum via l'API.
        // Le token n'est jamais placé dans l'URL du navigateur.
        if ($this->isMobileFlow()) {
            $code = Str::random(48);
            Cache::put('social-login:' . $code, $user->id, now()->addMinutes(5));

            return $this->mobileRedirect('code=' . urlencode($code));
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'vendeur') {
            return redirect()->route('vendeur.dashboard');
        }

        // Client : accueil après connexion (le dashboard reste accessible via « Mon espace »).
        return redirect('/');
    }

    private function isMobileFlow(): bool
    {
        return (bool) session('social_mobile', false);
    }

    /** Redirige le navigateur vers l'app avec le code ou l'erreur. */
    private function mobileRedirect(string $query)
    {
        $returnTo = session()->pull('social_return_to') ?: 'ecommerce://auth';
        session()->forget('social_mobile');

        $separator = str_contains($returnTo, '?') ? '&' : '?';

        return redirect()->away($returnTo . $separator . $query);
    }

    /**
     * N'accepte que les retours vers l'app (scheme ecommerce ou Expo Go).
     * Évite qu'un return_to arbitraire serve de redirection ouverte.
     */
    private function safeReturnTo($returnTo): string
    {
        $returnTo = trim((string) $returnTo);

        if ($returnTo === '') {
            return 'ecommerce://auth';
        }

        if (preg_match('#^ecommerce:///?auth(?:$|[/?])#', $returnTo)
            || preg_match('#^exp[s]?://[^/]+/--/auth(?:$|[/?])#', $returnTo)) {
            return $returnTo;
        }

        return 'ecommerce://auth';
    }
}
