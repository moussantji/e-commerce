<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Commandes;
use App\Models\Paniers;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Commandes::where('user_id', $request->user()->id)
            ->with('produits.photos')
            ->withCount('produits')
            ->latest()
            ->paginate((int) $request->query('per_page', 15));

        return OrderResource::collection($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Commandes::where('user_id', $request->user()->id)
            ->with('produits.photos')
            ->withCount('produits')
            ->findOrFail($id);

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address_id' => 'nullable|integer',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        $panier = Paniers::where('user_id', $user->id)->latest('id')->first();
        if (!$panier || $panier->products()->count() === 0) {
            return response()->json(['message' => 'Votre panier est vide.'], 422);
        }

        $panier->load('products');
        $sousTotal = 0;
        $lines = [];
        foreach ($panier->products as $p) {
            $qty = (int) $p->pivot->quantite;
            $pu = (float) $p->pivot->prix_unitaire;
            $lt = (float) ($p->pivot->total_ligne ?? $qty * $pu);
            $sousTotal += $lt;
            $lines[$p->id] = [
                'quantite' => $qty,
                'prix_unitaire' => $pu,
                'total' => $lt,
            ];
        }

        // Coupon
        $promo = null;
        $discount = 0;
        if (!empty($data['coupon_code'])) {
            $promo = PromoCode::where('code', $data['coupon_code'])->first();
            if (!$promo || !$promo->isValid()) {
                return response()->json(['message' => 'Code promo invalide ou expiré.'], 422);
            }
            $discount = $promo->calculateDiscount($sousTotal);
        }

        // Adresse : choisie, sinon par défaut, sinon la plus récente
        $address = null;
        if (!empty($data['address_id'])) {
            $address = $user->addresses()->find($data['address_id']);
        }
        if (!$address) {
            $address = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->latest()->first();
        }

        $fraisLivraison = 0;
        $livraison = \App\Models\Livraison::where('is_active', true)->first();
        if ($livraison) {
            $fraisLivraison = (float) ($livraison->price ?? 0);
        }
        $paiementId = \App\Models\Paiements::where('is_active', true)->value('id');

        $total = max(0, $sousTotal + $fraisLivraison - $discount);

        $adr = $address ? [
            'nom' => $address->nom,
            'telephone' => $address->telephone,
            'adresse' => $address->adresse,
            'ville' => $address->ville,
            'region' => $address->region,
            'pays' => $address->pays,
            'code_postal' => $address->code_postal,
        ] : [];

        $order = Commandes::create([
            'user_id' => $user->id,
            'paiement_id' => $paiementId,
            'livraison_id' => $livraison?->id,
            'numero_commande' => 'CMD-' . strtoupper(Str::random(8)),
            'statut' => 'en_attente',
            'sous_total' => $sousTotal,
            'frais_livraison' => $fraisLivraison,
            'promo_code_id' => $promo?->id,
            'promo_discount' => $discount,
            'remise' => 0,
            'total' => $total,
            'adresse_livraison' => $adr,
            'adresse_facturation' => $adr,
            'notes' => $data['notes'] ?? null,
            'date_en_attente' => now(),
        ]);

        foreach ($lines as $produitId => $pivot) {
            $order->produits()->attach($produitId, $pivot);
        }

        if ($promo) {
            $promo->increment('usage_count');
        }

        // Vider le panier
        $panier->products()->detach();

        $order->loadCount('produits')->load('produits.photos');

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function cancel(Request $request, $id)
    {
        $order = Commandes::where('user_id', $request->user()->id)->findOrFail($id);

        if (!in_array($order->statut, ['en_attente', 'traitement'], true)) {
            return response()->json(
                ['message' => 'Cette commande ne peut plus être annulée.'],
                422,
            );
        }

        $reason = $request->input('reason');
        $order->statut = 'annule';
        if ($reason) {
            $order->notes = trim(
                ($order->notes ? $order->notes . "\n" : '') . 'Annulation : ' . $reason,
            );
        }
        $order->save();

        $order->loadCount('produits')->load('produits.photos');

        return new OrderResource($order);
    }
}
