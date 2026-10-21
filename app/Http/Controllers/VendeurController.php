<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Categories;
use App\Models\Commandes;
use App\Models\Produits;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\CompteVendeurCree;
use App\Rules\MalianPhone;
use App\Support\AdminNotifier;
use App\Support\ImageUploads;
use App\Support\PhoneNumber;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
            'uploaded_images' => 'nullable|array|max:10',
            'uploaded_images.*' => 'string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['vendeur_id'] = $this->vendeurId();
        $validated['is_active'] = $request->boolean('is_active');

        $tags = $validated['tags'] ?? [];
        $uploadedImages = $request->input('uploaded_images', []);
        unset($validated['tags'], $validated['images'], $validated['uploaded_images']);

        $produit = Produits::create($validated);

        if (! empty($tags)) {
            $produit->tags()->sync($tags);
        }
        if ($request->hasFile('images')) {
            $produit->attachfiles($request->file('images'));
        }
        if (is_array($uploadedImages) && $uploadedImages !== [] && $request->user()) {
            ImageUploads::attachToProduct($produit, $uploadedImages, $request->user());
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
            'sku' => 'nullable|string|max:100|unique:produits,sku,'.$product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048',
            'uploaded_images' => 'nullable|array|max:10',
            'uploaded_images.*' => 'string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $tags = $validated['tags'] ?? [];
        $uploadedImages = $request->input('uploaded_images', []);
        unset($validated['tags'], $validated['images'], $validated['uploaded_images']);

        $product->update($validated);
        $product->tags()->sync($tags);
        if ($request->hasFile('images')) {
            $product->attachfiles($request->file('images'));
        }
        if (is_array($uploadedImages) && $uploadedImages !== [] && $request->user()) {
            ImageUploads::attachToProduct($product, $uploadedImages, $request->user());
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
     * Demande publique de compte vendeur (depuis la modale) — EN 1 ÉTAPE.
     * Crée directement le compte INACTIF, en attente de validation
     * par l'admin. Plus de code WhatsApp demandé à la création
     * (le numéro pourra être vérifié plus tard depuis le profil).
     */
    public function demande(Request $request)
    {
        $validated = $request->validateWithBag('vendeur', [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'tel' => ['required', 'string', 'max:20', new MalianPhone],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $tel = PhoneNumber::normalize($validated['tel']);

        if (! $tel) {
            return back()
                ->withErrors(['tel' => 'Numéro WhatsApp invalide : 8 chiffres maliens attendus (ex : 70 00 00 00).'], 'vendeur')
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $vendeur = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'tel' => $tel,
            'tel_verified_at' => null,
            'password' => Hash::make($validated['password']),
            'role' => 'vendeur',
            'status' => 'inactive',
        ]);

        // Notifie les admins (avec lien vers la fiche).
        AdminNotifier::notifyPayment(
            'Nouveau vendeur à valider',
            "{$vendeur->name} ({$vendeur->email} · ".PhoneNumber::pretty($tel).') demande un compte vendeur.',
            ['type' => 'admin_user', 'id' => $vendeur->id],
        );

        // Email de confirmation au vendeur.
        try {
            $vendeur->notify(new CompteVendeurCree);
        } catch (\Throwable $e) {
            Log::warning('Email vendeur échoué : '.$e->getMessage());
        }

        // Redirection WhatsApp avec ses informations.
        $msg = "Bonjour, je viens de créer mon compte vendeur : {$vendeur->name} ({$vendeur->email} · ".PhoneNumber::pretty($tel).'). Merci de le valider.';
        $wa = WhatsApp::link($msg);

        return back()->with('vendeur_created', [
            'nom' => $vendeur->name,
            'email' => $vendeur->email,
            'tel' => PhoneNumber::pretty($tel),
            'wa' => $wa,
        ]);
    }

    /**
     * Ancienne ÉTAPE 1 (envoi du code WhatsApp) — conservée en alias :
     * crée directement le compte via demande(), sans code.
     */
    public function demandeCode(Request $request)
    {
        return $this->demande($request);
    }
}
