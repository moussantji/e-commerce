<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Produits;
use App\Models\Commandes;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view("commande.show", [
            "commande" => $id,
            'categories' => $categories,
        ]);
    }

    public function destroy(Commandes $commande)
    {
        $commande->delete();
        return redirect()->route('dashboard')->with('success', 'Supprimé!');
    }
}
