<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Maintient users.last_activity à jour pendant la navigation.
 * Sans ça, le statut "en ligne" du dashboard (seuil 5 min) retombe à
 * "inactif" alors que l'utilisateur est encore connecté, car last_activity
 * n'était écrit qu'à la connexion.
 */
class UpdateLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = Auth::user();
            if ($user && (!$user->last_activity || $user->last_activity->lt(now()->subMinutes(2)))) {
                $user->forceFill(['last_activity' => now()])->saveQuietly();
            }
        } catch (\Throwable $e) {
            // silencieux : ne jamais casser la réponse pour un suivi de présence
        }

        return $response;
    }
}
