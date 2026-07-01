<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Connexion / inscription via un token social obtenu côté mobile
     * (Google / Facebook). Le client envoie le access_token, on le vérifie
     * auprès du fournisseur via Socialite (mode stateless).
     */
    public function social(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:google,facebook',
            'access_token' => 'required|string',
        ]);

        try {
            $socialUser = Socialite::driver($data['provider'])->stateless()->userFromToken($data['access_token']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Jeton social invalide ou expiré.'], 422);
        }

        $email = $socialUser->getEmail();
        if (!$email) {
            return response()->json(['message' => "Le fournisseur n'a pas communiqué d'adresse e-mail."], 422);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Utilisateur'),
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'role' => 'customer',
                'status' => 'active',
                'provider' => $data['provider'],
                'provider_id' => $socialUser->getId(),
            ]);

            try {
                $user->notify(new \App\Notifications\WelcomeNotification());
            } catch (\Throwable $e) {
                // ignore
            }
        } else {
            $user->forceFill([
                'provider' => $data['provider'],
                'provider_id' => $socialUser->getId(),
                'last_login' => now(),
                'last_activity' => now(),
            ])->save();
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'ville' => $user->ville,
                'pays' => $user->pays,
            ],
            'token' => $token,
        ]);
    }
}
