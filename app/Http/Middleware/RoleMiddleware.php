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
    public function handle(Request $request, Closure $next, $role): Response
    {

        // Accepte plusieurs rôles séparés par des virgules : role:customer,vendeur
        $allowed = array_map('trim', explode(',', (string) $role));

        if(!in_array($request->user()->role, $allowed, true))
        {
            abort(404);
        }

        return $next($request);
    }
}
