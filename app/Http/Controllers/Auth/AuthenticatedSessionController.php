<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->recordLogin($request);

        if($request->user()->role === 'admin')
        {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Enregistre la connexion : date + position géographique (par IP).
     * N'empêche jamais la connexion en cas d'échec.
     */
    protected function recordLogin(Request $request): void
    {
        try {
            $user = $request->user();
            $data = ['last_login' => now(), 'last_activity' => now()];

            $geo = \App\Support\IpGeolocator::locate($request->ip());
            if ($geo && $geo['lat'] !== null && $geo['lon'] !== null) {
                $data['latitude'] = $geo['lat'];
                $data['longitude'] = $geo['lon'];
                $data['ville'] = $geo['city'] ?? $user->ville;
                $data['region'] = $geo['region'] ?? $user->region;
                $data['pays'] = $geo['country'] ?? $user->pays;
            }

            $user->forceFill($data)->save();
        } catch (\Throwable $e) {
            // silencieux : la connexion ne doit jamais échouer à cause du géocodage
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
