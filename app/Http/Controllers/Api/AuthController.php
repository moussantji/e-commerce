<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        try {
            $user->notify(new WelcomeNotification());
        } catch (\Throwable $e) {
            // ne bloque pas l'inscription si l'email échoue
        }

        return response()->json([
            'user' => $this->userPayload($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        $user->forceFill(['last_login' => now(), 'last_activity' => now()])->save();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $this->userPayload($user),
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }

    /** Mise à jour du profil (mobile). L'email n'est pas modifiable. */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:30',
            'ville' => 'nullable|string|max:120',
            'pays' => 'nullable|string|max:120',
            'region' => 'nullable|string|max:120',
            'lieu_naiss' => 'nullable|string|max:120',
        ]);

        $user->fill($data)->save();

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user' => $this->userPayload($user->fresh()),
        ]);
    }

    /** Changement de mot de passe (sécurité du compte). */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Le mot de passe actuel est incorrect.'],
            ]);
        }

        $user->forceFill(['password' => Hash::make($data['password'])])->save();

        // Révoque les autres tokens, garde la session courante
        $currentId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentId)->delete();

        return response()->json(['message' => 'Mot de passe mis à jour.']);
    }

    /** Changement de photo de profil (avatar). */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:20480',
        ]);

        $user = $request->user();

        // Supprime l'ancienne photo (fichier + ligne) puis attache la nouvelle
        $user->photos()->get()->each(fn ($p) => $p->delete());
        $user->attachfiles([$request->file('avatar')]);

        return response()->json([
            'message' => 'Photo de profil mise à jour.',
            'user' => $this->userPayload($user->fresh()),
        ]);
    }

    /** Enregistre le token de notification push Expo du téléphone. */
    public function savePushToken(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string|max:255',
        ]);

        $request->user()->forceFill(['expo_push_token' => $data['token']])->save();

        return response()->json(['message' => 'Token enregistré.']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'tel' => $user->tel,
            'role' => $user->role,
            'ville' => $user->ville,
            'pays' => $user->pays,
            'region' => $user->region,
            'lieu_naiss' => $user->lieu_naiss,
            'avatar' => $this->avatarUrl($user),
        ];
    }

    /** URL absolue de la photo de profil, ou null. */
    private function avatarUrl(User $user): ?string
    {
        $photo = method_exists($user, 'getPhoto') ? $user->getPhoto() : null;
        if (!$photo) {
            return null;
        }
        $url = $photo->getImageUrl(300, 300);
        if (!$url) {
            return null;
        }
        if (str_starts_with($url, 'http')) {
            return $url;
        }
        return rtrim(request()->getSchemeAndHttpHost(), '/') . '/' . ltrim($url, '/');
    }
}
