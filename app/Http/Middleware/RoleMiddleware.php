<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        // Rôles acceptés : role:admin ou role:customer,vendeur
        // (Laravel découpe déjà les virgules en paramètres).
        $allowed = [];
        foreach ($roles as $role) {
            foreach (explode(',', (string) $role) as $r) {
                $r = trim($r);
                if ($r !== '') {
                    $allowed[] = $r;
                }
            }
        }

        if(!in_array($request->user()->role, $allowed, true))
        {
            abort(404);
        }

        return $next($request);
    }
}
