<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::query()->withCount('orders');

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        switch ($request->get('filter')) {
            case 'active':
                $query->where('status', 'active');
                break;
            case 'banned':
                $query->where('status', 'inactive'); // ou ta logique de ban
                break;
            case 'admins':
                $query->where('role', 'admin');
                break;
            case 'new':
                $query->where('created_at', '>', now()->subDays(7));
                break;
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $users->getCollection()->transform(function ($u) {
            $u->setAttribute('total_spent', (float) $u->orders()->sum('total'));
            return $u;
        });

        $counts = [
            'all'    => User::count(),
            'active' => User::where('status', 'active')->count(),
            'banned' => User::where('status', 'inactive')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'new'    => User::where('created_at', '>', now()->subDays(7))->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'prenom'        => ['nullable', 'string', 'max:255'],
            'date_naiss'    => ['nullable', 'date'],
            'lieu_naiss'    => ['nullable', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'tel'           => ['nullable', 'string', 'max:50', 'unique:users,tel'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'role'          => ['required', 'in:admin,customer'],
            'status'        => ['required', 'in:active,inactive'],
            'pays'          => ['nullable', 'string', 'max:100'],
            'adresse'       => ['nullable', 'array'],
            'social_links'  => ['nullable', 'array'],
            'email_verified' => ['nullable', 'boolean'],
        ]);

        // Préparation des champs JSON et dates
        $data['adresse']       = $data['adresse'] ?? null;          // sera casté en JSON si cast dans le modèle
        $data['social_links']  = $data['social_links'] ?? null;
        $data['password']      = Hash::make($data['password']);
        $data['email_verified_at'] = !empty($data['email_verified']) ? now() : null;

        // Champs non envoyés mais existants dans la table
        unset($data['email_verified']);

        // Création de l'utilisateur
        $user = User::create($data);

        // Si tu utilises spatie/permission
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($data['role']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        $user->load('orders');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,customer'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Mise à jour du mot de passe si fourni
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Compatibilité Spatie si présent
        if (method_exists($user, 'syncRoles')) {
            try {
                $user->syncRoles([$validated['role']]);
            } catch (\Throwable $e) {
                // rôle géré via la colonne 'role'
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Change rapidement le rôle d'un utilisateur (admin <-> customer).
     */
    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,customer'],
        ]);

        if (auth()->id() === $user->id && $data['role'] !== 'admin') {
            return back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('success', "Rôle de {$user->name} mis à jour : {$data['role']}.");
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        // Vérifier si l'utilisateur a des commandes
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer un utilisateur avec des commandes existantes');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Affiche l'historique des commandes d'un utilisateur
     */
    public function orders(User $user)
    {
        $orders = $user->orders()->with('items.product')->latest()->paginate(10);
        return view('admin.users.orders', compact('user', 'orders'));
    }
}
