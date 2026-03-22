<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Produits;
use App\Models\Commandes;
use App\Models\Categories;
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

        return view("welcome", [
            'categories' => $categories,
            'banners' => $banners,
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
        $commande = Commandes::findorfail($id);
        $commande->delete();
        return redirect()->route('dashboard')->with('success', 'Supprimé!');
    }
}
