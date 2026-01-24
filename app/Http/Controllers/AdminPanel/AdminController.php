<?php

namespace App\Http\Controllers\AdminPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administration
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Affiche la page des paramètres
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Met à jour les paramètres du site
     */
    public function updateSettings(Request $request)
    {
        // Logique de mise à jour des paramètres
        return redirect()->route('admin.settings')
            ->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Affiche le formulaire de profil administrateur
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Met à jour les informations du profil administrateur
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'tel' => 'nullable|string|max:20',
            'date_naiss' => 'nullable|date',
            'lieu_naiss' => 'nullable|string|max:255',
            'pays' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'adresse' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255'
        ]);

        try {
            // Préparation des données pour la mise à jour
            $userData = [
                'name' => $validated['name'],
                'prenom' => $validated['prenom'] ?? $user->prenom,
                'email' => $validated['email'],
                'tel' => $validated['tel'] ?? $user->tel,
                'date_naiss' => $validated['date_naiss'] ?? $user->date_naiss,
                'lieu_naiss' => $validated['lieu_naiss'] ?? $user->lieu_naiss,
                'pays' => $validated['pays'] ?? $user->pays,
                'region' => $validated['region'] ?? $user->region,
                'last_activity' => now()
            ];

            // Gestion de l'adresse
            if (isset($validated['adresse'])) {
                $adresseData = [
                    'adresse' => $validated['adresse'],
                    'pays' => $validated['pays'] ?? $user->pays,
                    'region' => $validated['region'] ?? $user->region
                ];
                $userData['adresse'] = json_encode($adresseData);
            } else {
                // Si l'adresse est vide, on la met à jour avec une chaîne vide
                $userData['adresse'] = '';
            }

            // Gestion des réseaux sociaux (stockés dans un champ JSON)
            $socialLinks = [];
            if (!empty($validated['facebook_url'] ?? null)) {
                $socialLinks['facebook'] = $validated['facebook_url'];
            }
            if (!empty($validated['twitter_url'] ?? null)) {
                $socialLinks['twitter'] = $validated['twitter_url'];
            }
            if (!empty($validated['instagram_url'] ?? null)) {
                $socialLinks['instagram'] = $validated['instagram_url'];
            }
            if (!empty($validated['linkedin_url'] ?? null)) {
                $socialLinks['linkedin'] = $validated['linkedin_url'];
            }

            // Mise à jour des réseaux sociaux
            if (!empty($socialLinks)) {
                // Si l'utilisateur a déjà des réseaux sociaux, on les récupère
                $existingSocialLinks = [];
                if (!empty($user->social_links)) {
                    $existingSocialLinks = is_string($user->social_links)
                        ? json_decode($user->social_links, true)
                        : $user->social_links;
                    $existingSocialLinks = is_array($existingSocialLinks) ? $existingSocialLinks : [];
                }

                // Fusion avec les nouveaux réseaux sociaux
                $socialLinks = array_merge($existingSocialLinks, $socialLinks);
                $userData['social_links'] = json_encode($socialLinks);
            }

            // Journalisation des données avant mise à jour

            // Mise à jour de l'utilisateur
            $updated = $user->update($userData);


            // Dans BrandController::update() ET ::destroy()
            if ($request->hasFile('avatar')) {
                // ✅ 1️⃣ SUPPRIME FICHIERS d'ABORD
                foreach ($user->photos as $photo) {
                    Storage::disk('public')->delete($photo->filename);
                }

                // ✅ 2️⃣ SUPPRIME DB
                $user->photos()->delete();
            }

            // ✅ 2️⃣ ENSUITE attacher logo
            if ($request->hasFile('avatar')) {
                $user->attachfiles([$request->file('avatar')]);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating user profile: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du profil');
        }

        return redirect()->route('admin.profile')
            ->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Met à jour le mot de passe de l'administrateur
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Mot de passe mis à jour avec succès');
    }
}
