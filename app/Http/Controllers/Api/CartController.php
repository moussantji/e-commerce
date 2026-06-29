<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paniers;
use App\Models\Produits;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function panier(Request $request): Paniers
    {
        $userId = $request->user()->id;
        $panier = Paniers::where('user_id', $userId)->latest('id')->first();

        if (!$panier) {
            $panier = new Paniers();
            // date_maj est NOT NULL sans valeur par défaut
            $panier->forceFill(['user_id' => $userId, 'date_maj' => now()])->save();
        }

        return $panier;
    }

    public function index(Request $request)
    {
        return response()->json($this->payload($this->panier($request)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:produits,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Produits::findOrFail($data['product_id']);
        $qty = (int) ($data['quantity'] ?? 1);

        $panier = $this->panier($request);
        $existing = $panier->products()->where('produits_id', $product->id)->first();
        $current = $existing ? (int) $existing->pivot->quantite : 0;

        if ($product->stock < ($current + $qty)) {
            return response()->json([
                'message' => "Stock insuffisant pour {$product->name} (disponible : {$product->stock}).",
            ], 422);
        }

        $prix = (float) ($product->sale_price ?? $product->price);
        $newQty = $current + $qty;

        if ($existing) {
            $panier->products()->updateExistingPivot($product->id, [
                'quantite' => $newQty,
                'prix_unitaire' => $prix,
                'total_ligne' => $newQty * $prix,
            ]);
        } else {
            $panier->products()->attach($product->id, [
                'quantite' => $newQty,
                'prix_unitaire' => $prix,
                'total_ligne' => $newQty * $prix,
            ]);
        }

        return response()->json($this->payload($panier->fresh()));
    }

    public function update(Request $request, $productId)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1']);
        $panier = $this->panier($request);
        $product = Produits::findOrFail($productId);

        if ($product->stock < $data['quantity']) {
            return response()->json([
                'message' => "Stock insuffisant (disponible : {$product->stock}).",
            ], 422);
        }

        $prix = (float) ($product->sale_price ?? $product->price);
        $panier->products()->updateExistingPivot($productId, [
            'quantite' => $data['quantity'],
            'prix_unitaire' => $prix,
            'total_ligne' => $data['quantity'] * $prix,
        ]);

        return response()->json($this->payload($panier->fresh()));
    }

    public function destroy(Request $request, $productId)
    {
        $panier = $this->panier($request);
        $panier->products()->detach($productId);

        return response()->json($this->payload($panier->fresh()));
    }

    private function payload(Paniers $panier): array
    {
        $panier->load(['products.photos']);

        $items = $panier->products->map(function ($p) {
            $photo = $p->getPhoto();
            $img = $photo ? $photo->getImageUrl(300, 300) : asset('assets/img/products/1.png');
            return [
                'product_id' => $p->id,
                'name' => $p->name,
                'image' => str_starts_with($img, 'http') ? $img : rtrim(config('app.url'), '/') . '/' . ltrim($img, '/'),
                'quantity' => (int) $p->pivot->quantite,
                'unit_price' => (float) $p->pivot->prix_unitaire,
                'line_total' => (float) $p->pivot->total_ligne,
                'stock' => (int) $p->stock,
            ];
        });

        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'total' => round($items->sum('line_total'), 2),
        ];
    }
}
