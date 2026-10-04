<?php

namespace App\Http\Controllers;

use App\Models\Commandes;
use App\Models\Produits;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Espace vendeur : tableau de bord, ses produits et les commandes
 * contenant ses produits. Chaque vendeur ne voit que ses données.
 */
class VendeurController extends Controller
{
    private function vendeurId(): int
    {
        return Auth::id();
    }

    public function dashboard()
    {
        $vid = $this->vendeurId();

        $produits = Produits::where('vendeur_id', $vid);
        $nbProduits = (clone $produits)->count();
        $stockBas = (clone $produits)->where('stock', '<=', 5)->count();

        $commandeIds = \DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('produits.vendeur_id', $vid)
            ->distinct()
            ->pluck('commande_produit.commande_id');

        $commandes = Commandes::with(['user', 'produits'])
            ->whereIn('id', $commandeIds)
            ->latest()
            ->take(8)
            ->get();

        $revenus = (float) \DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('produits.vendeur_id', $vid)
            ->sum('commande_produit.total');

        $topProduits = Produits::select('produits.id', 'produits.name')
            ->selectRaw('COALESCE(SUM(commande_produit.quantite), 0) as vendus')
            ->leftJoin('commande_produit', 'commande_produit.produit_id', '=', 'produits.id')
            ->where('produits.vendeur_id', $vid)
            ->groupBy('produits.id', 'produits.name')
            ->orderByDesc('vendus')
            ->take(5)
            ->get();

        return view('vendeur.dashboard', [
            'nbProduits' => $nbProduits,
            'stockBas' => $stockBas,
            'nbCommandes' => $commandeIds->count(),
            'revenus' => $revenus,
            'commandes' => $commandes,
            'topProduits' => $topProduits,
        ]);
    }

    public function products(Request $request)
    {
        $query = Produits::with(['category', 'brand'])
            ->where('vendeur_id', $this->vendeurId());

        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%");
            });
        }

        return view('vendeur.products.index', [
            'products' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function createProduct()
    {
        return view('vendeur.products.form', [
            'product' => null,
            'categories' => Categories::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:produits,sku',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['vendeur_id'] = $this->vendeurId();
        $validated['is_active'] = $request->boolean('is_active');

        $tags = $validated['tags'] ?? [];
        unset($validated['tags'], $validated['images']);

        $produit = Produits::create($validated);

        if (!empty($tags)) {
            $produit->tags()->sync($tags);
        }
        if ($request->hasFile('images')) {
            $produit->attachfiles($request->file('images'));
        }

        return redirect()->route('vendeur.products.index')
            ->with('success', 'Produit créé avec succès !');
    }

    public function editProduct(Produits $product)
    {
        abort_unless((int) $product->vendeur_id === $this->vendeurId(), 403);

        return view('vendeur.products.form', [
            'product' => $product->load(['tags', 'photos']),
            'categories' => Categories::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function updateProduct(Request $request, Produits $product)
    {
        abort_unless((int) $product->vendeur_id === $this->vendeurId(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:produits,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $tags = $validated['tags'] ?? [];
        unset($validated['tags'], $validated['images']);

        $product->update($validated);
        $product->tags()->sync($tags);
        if ($request->hasFile('images')) {
            $product->attachfiles($request->file('images'));
        }

        return redirect()->route('vendeur.products.index')
            ->with('success', 'Produit mis à jour !');
    }

    public function destroyProduct(Produits $product)
    {
        abort_unless((int) $product->vendeur_id === $this->vendeurId(), 403);

        $product->delete();

        return redirect()->route('vendeur.products.index')
            ->with('success', 'Produit supprimé !');
    }

    public function orders()
    {
        $vid = $this->vendeurId();

        $commandeIds = \DB::table('commande_produit')
            ->join('produits', 'produits.id', '=', 'commande_produit.produit_id')
            ->where('produits.vendeur_id', $vid)
            ->distinct()
            ->pluck('commande_produit.commande_id');

        return view('vendeur.orders.index', [
            'commandes' => Commandes::with(['user', 'produits'])
                ->whereIn('id', $commandeIds)
                ->latest()
                ->paginate(15),
        ]);
    }

    /**
     * Demande publique de compte vendeur (depuis la modale) — ÉTAPE 1.
     * Valide les infos, les met de côté (mot de passe déjà haché) et envoie
     * un code de vérification sur le WhatsApp du numéro saisi.
     * Si seules les infos minimales (tel) sont renvoyées et qu'une demande
     * est déjà en attente pour ce numéro, on se contente de renvoyer le code.
     */
    public function demandeCode(Request $request)
    {
        $tel = \App\Support\PhoneNumber::normalize($request->input('tel'));

        if (!$tel) {
            return back()
                ->withErrors(['tel' => 'Numéro WhatsApp invalide : 8 chiffres maliens attendus (ex : 70 00 00 00).'], 'vendeur')
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $flag = ['tel' => $tel, 'pretty' => \App\Support\PhoneNumber::pretty($tel)];

        // Renvoi du code (demande déjà en attente pour ce numéro).
        if (!$request->filled('name') && \App\Support\WhatsAppVerification::peekPending($tel)) {
            try {
                \App\Support\WhatsAppVerification::send($tel);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Envoi OTP WhatsApp échoué : ' . $e->getMessage());

                return back()->with('vendeur_code_sent', $flag)
                    ->withErrors(['code' => 'Envoi WhatsApp impossible pour le moment. Réessayez.'], 'vendeur');
            }

            return back()->with('vendeur_code_sent', $flag)
                ->with('success', 'Nouveau code envoyé sur WhatsApp au ' . $flag['pretty'] . '.');
        }

        $validated = $request->validateWithBag('vendeur', [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'tel' => ['required', 'string', 'max:20', new \App\Rules\MalianPhone()],
            'password' => 'required|string|min:8|confirmed',
        ]);

        \App\Support\WhatsAppVerification::stashPending($tel, [
            'tel' => $tel,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
        ]);

        try {
            \App\Support\WhatsAppVerification::send($tel);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Envoi OTP WhatsApp échoué : ' . $e->getMessage());

            return back()->with('vendeur_code_sent', $flag)
                ->withErrors(['code' => 'Envoi WhatsApp impossible pour le moment. Touchez « Renvoyer le code ».'], 'vendeur');
        }

        return back()->with('vendeur_code_sent', $flag)
            ->with('success', 'Code envoyé sur WhatsApp au ' . $flag['pretty'] . '.');
    }

    /**
     * Demande publique de compte vendeur (depuis la modale) — ÉTAPE 2.
     * Vérifie le code WhatsApp puis crée le compte INACTIF,
     * en attente de validation par l'admin (numéro déjà vérifié).
     */
    public function demande(Request $request)
    {
        $request->validateWithBag('vendeur', [
            'tel' => ['required', 'string', new \App\Rules\MalianPhone()],
            'code' => 'required|digits:6',
        ]);

        $tel = \App\Support\PhoneNumber::normalize($request->input('tel'));
        $flag = ['tel' => $tel, 'pretty' => \App\Support\PhoneNumber::pretty($tel)];
        $pending = $tel ? \App\Support\WhatsAppVerification::peekPending($tel) : null;

        if (!$pending || ($pending['tel'] ?? null) !== $tel) {
            return back()
                ->withErrors(['code' => 'Demande expirée. Recommencez depuis l\'étape 1.'], 'vendeur');
        }

        if (\App\Models\User::where('email', $pending['email'])->exists()) {
            \App\Support\WhatsAppVerification::clearPending($tel);

            return back()
                ->withErrors(['email' => 'Ce compte a déjà été créé. Connectez-vous.'], 'vendeur');
        }

        if (!\App\Support\WhatsAppVerification::check($tel, $request->input('code'))) {
            return back()->with('vendeur_code_sent', $flag)
                ->withErrors(['code' => 'Code incorrect ou expiré. Demandez un nouveau code.'], 'vendeur');
        }

        \App\Support\WhatsAppVerification::clearPending($tel);

        $vendeur = \App\Models\User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'tel' => $tel,
            'tel_verified_at' => now(),
            'password' => $pending['password_hash'],
            'role' => 'vendeur',
            'status' => 'inactive',
        ]);

        // Notifie les admins (avec lien vers la fiche).
        \App\Support\AdminNotifier::notifyPayment(
            'Nouveau vendeur à valider',
            "{$vendeur->name} ({$vendeur->email} · " . \App\Support\PhoneNumber::pretty($tel) . ') demande un compte vendeur.',
            ['type' => 'admin_user', 'id' => $vendeur->id],
        );

        // Email de confirmation au vendeur.
        try {
            $vendeur->notify(new \App\Notifications\CompteVendeurCree());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email vendeur échoué : ' . $e->getMessage());
        }

        // Redirection WhatsApp avec ses informations.
        $msg = "Bonjour, je viens de créer mon compte vendeur : {$vendeur->name} ({$vendeur->email} · " . \App\Support\PhoneNumber::pretty($tel) . '). Merci de le valider.';
        $wa = \App\Support\WhatsApp::link($msg);

        return back()->with('vendeur_created', [
            'nom' => $vendeur->name,
            'email' => $vendeur->email,
            'tel' => \App\Support\PhoneNumber::pretty($tel),
            'wa' => $wa,
        ]);
    }
}
