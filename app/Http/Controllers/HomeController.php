<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Produits;
use App\Models\Commandes;
use App\Models\Categories;
use App\Models\PromoCode;
use Database\Seeders\CommandesSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();

        $banners = Banner::with('photos')->latest()->take(3)->get();

        // Offres flash : les 6 plus grosses remises (prix soldé < prix, en stock, actifs).
        // Sans promo en cours, on retombe sur les 6 derniers produits.
        $selection = Produits::with(['photos', 'reviews'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->orderByRaw('(price - sale_price) / price DESC')
            ->take(6)
            ->get();

        if ($selection->isEmpty()) {
            $selection = Produits::with(['photos', 'reviews'])
                ->where('is_active', true)
                ->latest()
                ->take(6)
                ->get();
        }

        // Dernière nouveauté pour la tuile hero (devant NOUVEAUTÉS).
        $newSpotlight = Produits::with(['photos'])
            ->where('is_active', true)
            ->latest()
            ->first();

        // Compte à rebours flash : expiration du code promo valide le plus urgent.
        // Sans code daté valide, le compteur reste décoratif côté vue.
        $flashCode = PromoCode::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->orderBy('expires_at')
            ->first();

        return view("welcome", [
            'categories' => $categories,
            'banners' => $banners,
            'selection' => $selection,
            'promoSpotlight' => $selection->first(),
            'newSpotlight' => $newSpotlight,
            'flashCode' => $flashCode,
            'flashEndsAt' => $flashCode?->expires_at,
        ]);
    }


    public function produits(string $slug, int $id)
    {
        $produit = Produits::findOrFail($id);

        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();

        return view("customer.show", [
            'slug' => $slug,
            'produit' => $produit,
            'categories' => $categories,
        ]);
    }

    public function allProduits()
    {
        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();
        return view('produit', [
            'categories' => $categories,
        ]);
    }

    public function favoris()
    {
        // ✅ Redirige si NON connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Connectez-vous pour voir vos favoris');
        }

        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();
        return view('favoris', [
            'categories' => $categories
        ]);
    }

    public function commande(Commandes $id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Connectez-vous pour voir vos commandes');
        }
        $commande = $id->load('items.produit');

        // Une commande n'est visible que par son propriétaire ou un admin
        abort_unless($commande->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $categories = Categories::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->take(8)
            ->get();
        $commande = $id->load('items.produit');

        return view("commande.show", [
            "commande" => $commande,
            'categories' => $categories,
        ]);
    }

    public function exportPdf(Commandes $id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Connectez-vous pour exporter vos commandes');
        }

        // Export réservé au propriétaire ou à un admin
        abort_unless($id->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $commande = $id->load(["items.produit", "user", "paiement", "livraison", "promoCode"]);

        // Sécuriser les adresses au cas où ce soient des chaînes JSON
        $commande->adresse_facturation = is_array($commande->adresse_facturation)
            ? $commande->adresse_facturation
            : json_decode($commande->adresse_facturation, true) ?? [];

        $commande->adresse_livraison = is_array($commande->adresse_livraison)
            ? $commande->adresse_livraison
            : json_decode($commande->adresse_livraison, true) ?? [];

        if (! $commande->isPaid()) {
            return back()->with('error', 'Impossible d\'exporter le PDF : la commande n\'est pas payée.');
        }

        // Générer le HTML
        $html = view('pdf.commande', ['commande' => $commande, 'categories' => collect()])->render();

        // Nettoyer les espaces HTML (évite les sauts inutiles)
        $html = preg_replace('/>\s+</', '><', $html);

        // Créer DomPDF directement
        $pdf = PDF::loadHTML($html);
        // Forcer la taille de la page à 1 page A4
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('commande-' . $commande->id . '.pdf');
    }

    public function destroy(String $id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'Connectez-vous pour gérer vos commandes');
        }
        $commande = Commandes::findorfail($id);

        // Suppression réservée au propriétaire ou à un admin
        abort_unless($commande->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $commande->delete();
        return redirect()->route('dashboard')->with('success', 'Supprimé!');
    }
}
