<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Bloque les comptes inactifs (vendeur en attente de validation, client
     * banni / désactivé) même s'ils ont encore une session ou un token valide.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isApi = $request->is('api/*');

        // Les routes publiques de l'API restent accessibles (catalogue, etc.) :
        // on ne bloque que les routes qui exigent une authentification.
        if ($isApi && ! $this->requiresAuthentication($request)) {
            return $next($request);
        }

        $user = $isApi
            ? $request->user('sanctum')
            : $request->user();

        if (! $user || $user->isActive()) {
            return $next($request);
        }

        $message = $user->inactiveMessage();

        if ($isApi || $request->expectsJson()) {
            // Révoque tous les appareils : un compte banni ne doit plus avoir
            // de token utilisable.
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            return response()->json([
                'message' => $message,
                'code' => 'account_inactive',
            ], 401);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // On passe directement par l'accueil : /login redirige lui-même vers
        // l'accueil et consommerait le message flash avant son affichage.
        return redirect()
            ->route('home', ['connexion' => 1])
            ->with('error', $message);
    }

    /** La route exige-t-elle une authentification (middleware auth / auth:xxx) ? */
    private function requiresAuthentication(Request $request): bool
    {
        $route = $request->route();

        if (! $route) {
            return false;
        }

        foreach ($route->gatherMiddleware() as $middleware) {
            if ($middleware === 'auth' || str_starts_with((string) $middleware, 'auth:')) {
                return true;
            }
        }

        return false;
    }
}
