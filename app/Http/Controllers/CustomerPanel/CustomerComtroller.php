<?php

namespace App\Http\Controllers\CustomerPanel;

use App\Models\AvisClient;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerComtroller extends Controller
{
    public function dashboard()
    {
        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();
        $commandes = Auth::user()->orders()
            ->with(['produits']) // Si relation produits
            ->latest()
            ->get();
        $avis = AvisClient::with(['user', 'product'])
            ->latest()
            ->get();
        $wishlist = Auth::user()->wishlistProducts()
            ->with(['photos', 'caracteristiques'])
            ->get();
        return view('dashboard', [
            'categories' => $categories,
            'commandes' => $commandes,
            'avis' => $avis,
            'wishlist' => $wishlist
        ]);
    }

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

            // Changement d'email -> revérification requise.
            if (($validated['email'] ?? null) !== $user->email) {
                $userData['email_verified_at'] = null;
            }

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

        return redirect()->route('profile.edit')
            ->with('success', 'Profil mis à jour avec succès');
    }

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

        return redirect()->route('profile.edit')
            ->with('success', 'Mot de passe mis à jour avec succès');
    }
}
