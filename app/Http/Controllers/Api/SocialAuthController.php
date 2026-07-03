<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Connexion / inscription via Google depuis le mobile.
     *
     * Deux formats acceptés :
     *  - id_token : jeton OpenID Connect renvoyé par le SDK natif
     *    (@react-native-google-signin/google-signin) → vérifié via l'endpoint
     *    officiel Google (tokeninfo). C'est le flux recommandé (conforme à la
     *    policy OAuth 2.0 de Google).
     *  - access_token : ancien flux (expo-auth-session / web) → vérifié via
     *    Socialite en mode stateless. Conservé pour compatibilité.
     */
    public function social(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:google',
            'id_token' => 'nullable|string',
            'access_token' => 'nullable|string',
        ]);

        if (empty($data['id_token']) && empty($data['access_token'])) {
            return response()->json(['message' => 'Jeton manquant.'], 422);
        }

        try {
            $profile = ! empty($data['id_token'])
                ? $this->verifyGoogleIdToken($data['id_token'])
                : $this->profileFromAccessToken($data['access_token']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Jeton social invalide ou expiré.'], 422);
        }

        if (empty($profile['email'])) {
            return response()->json(['message' => "Le fournisseur n'a pas communiqué d'adresse e-mail."], 422);
        }

        $user = User::where('email', $profile['email'])->first();

        if (! $user) {
            $user = User::create([
                'name' => $profile['name'] ?: 'Utilisateur',
                'email' => $profile['email'],
                'password' => Hash::make(Str::random(40)),
                'role' => 'customer',
                'status' => 'active',
                'provider' => 'google',
                'provider_id' => $profile['id'],
            ]);

            try {
                $user->notify(new \App\Notifications\WelcomeNotification());
            } catch (\Throwable $e) {
                // ignore
            }
        } else {
            $user->forceFill([
                'provider' => 'google',
                'provider_id' => $profile['id'],
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

    /**
     * Vérifie un idToken Google auprès de l'endpoint officiel et renvoie le
     * profil { email, id, name }.
     */
    private function verifyGoogleIdToken(string $idToken): array
    {
        $resp = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (! $resp->ok()) {
            throw new \RuntimeException('idToken invalide');
        }

        $payload = $resp->json();

        // Vérifie l'audience UNIQUEMENT si des client IDs sont configurés côté
        // serveur. On accepte le client_id principal ainsi que la liste
        // `allowed_client_ids` (web / android / ios). Si rien n'est configuré,
        // on ne bloque pas (utile en dev / première intégration mobile).
        $allowedAudiences = array_values(array_filter(array_merge(
            [config('services.google.client_id')],
            (array) config('services.google.allowed_client_ids', []),
        )));

        if (! empty($allowedAudiences)
            && ! in_array($payload['aud'] ?? null, $allowedAudiences, true)) {
            throw new \RuntimeException('Audience du jeton invalide');
        }

        // L'e-mail doit être vérifié par Google.
        $verified = ($payload['email_verified'] ?? 'false');
        if ($verified !== true && $verified !== 'true') {
            throw new \RuntimeException('E-mail non vérifié');
        }

        return [
            'email' => $payload['email'] ?? null,
            'id' => $payload['sub'] ?? null,
            'name' => $payload['name'] ?? null,
        ];
    }

    /** Ancien flux : profil à partir d'un access_token via Socialite. */
    private function profileFromAccessToken(string $accessToken): array
    {
        $su = Socialite::driver('google')->stateless()->userFromToken($accessToken);

        return [
            'email' => $su->getEmail(),
            'id' => $su->getId(),
            'name' => $su->getName() ?: $su->getNickname(),
        ];
    }
}
