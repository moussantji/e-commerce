<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function destroy(Request $request, $produitId)
    {
        $user = auth()->user();
        $user->wishlistProducts()->detach($produitId);
        return redirect()->route('dashboard')->with('success', 'Produit retiré de votre liste de souhaits.');
    }
}
