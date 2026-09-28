<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Commandes;
use App\Models\Paiements;
use App\Models\Paniers;
use App\Models\PaymentProof;
use App\Models\Produits;
use App\Models\PromoCode;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Support\AdminNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'livraison_id' => 'nullable|integer|exists:livraisons,id',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        $panier = Paniers::where('user_id', $user->id)->latest('id')->first();
        if (!$panier || $panier->products()->count() === 0) {
            return response()->json(['message' => 'Votre panier est vide.'], 422);
        }

        $panier->load('products');

        // Coupon validation (before transaction to avoid holding locks during external checks)
        $promo = null;
        $discount = 0;
        if (!empty($data['coupon_code'])) {
            $promo = PromoCode::where('code', $data['coupon_code'])->first();
            if (!$promo || !$promo->isValid()) {
                return response()->json(['message' => 'Code promo invalide ou expiré.'], 422);
            }
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
        // Méthode de livraison choisie, sinon la première active
        $livraison = null;
        if (!empty($data['livraison_id'])) {
            $livraison = \App\Models\Livraison::where('is_active', true)
                ->find($data['livraison_id']);
        }
        if (!$livraison) {
            $livraison = \App\Models\Livraison::where('is_active', true)->first();
        }
        if ($livraison) {
            $fraisLivraison = (float) ($livraison->price ?? 0);
        }
        $paiementId = \App\Models\Paiements::where('is_active', true)->value('id');

        $adr = $address ? [
            'nom' => $address->nom,
            'telephone' => $address->telephone,
            'adresse' => $address->adresse,
            'ville' => $address->ville,
            'region' => $address->region,
            'pays' => $address->pays,
            'code_postal' => $address->code_postal,
        ] : [];

        // Wrap the entire order creation in a database transaction with pessimistic locking
        $order = DB::transaction(function () use ($panier, $user, $promo, $fraisLivraison, $livraison, $paiementId, $adr, $data) {
            $sousTotal = 0;
            $lines = [];

            // Lock products for update to prevent race conditions
            $productIds = $panier->products->pluck('id')->toArray();
            $lockedProducts = Produits::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($panier->products as $p) {
                $qty = (int) $p->pivot->quantite;
                $pu = (float) $p->pivot->prix_unitaire;
                $lt = (float) ($p->pivot->total_ligne ?? $qty * $pu);

                // Validate stock availability with the locked row
                $lockedProduct = $lockedProducts->get($p->id);
                if (!$lockedProduct || $lockedProduct->stock < $qty) {
                    throw new \App\Exceptions\InsufficientStockException(
                        "Stock insuffisant pour « {$p->name} ». Disponible : " . ($lockedProduct->stock ?? 0) . ", demandé : {$qty}."
                    );
                }

                $sousTotal += $lt;
                $lines[$p->id] = [
                    'quantite' => $qty,
                    'prix_unitaire' => $pu,
                    'total' => $lt,
                ];
            }

            // Calculate coupon discount with the validated sub-total
            $discount = 0;
            if ($promo) {
                // Re-check coupon validity with lock to prevent over-redemption
                $promo = PromoCode::where('id', $promo->id)->lockForUpdate()->first();
                if (!$promo || !$promo->isValid()) {
                    throw new \App\Exceptions\InvalidCouponException('Code promo invalide ou expiré.');
                }
                $discount = $promo->calculateDiscount($sousTotal);
            }

            $total = max(0, $sousTotal + $fraisLivraison - $discount);

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

            // Attach products to order and decrement stock atomically
            foreach ($lines as $produitId => $pivot) {
                $order->produits()->attach($produitId, $pivot);

                // Decrement stock for each product
                Produits::where('id', $produitId)->decrement('stock', $pivot['quantite']);
            }

            // Atomically increment coupon usage count
            if ($promo) {
                $promo->increment('usage_count');
            }

            // Clear the cart
            $panier->products()->detach();

            return $order;
        });

        $order->loadCount('produits')->load('produits.photos');

        // Confirmation de création de commande (base + email + push)
        try {
            $user->notify(new \App\Notifications\OrderStatusNotification(
                $order,
                'Commande créée — en attente de paiement',
            ));
        } catch (\Throwable $e) {
            /* ignore */
        }

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function cancel(Request $request, $id)
    {
        $order = Commandes::where('user_id', $request->user()->id)->findOrFail($id);

        if (!in_array($order->statut, ['en_attente', 'paiement_declare', 'payee'], true)) {
            return response()->json(
                ['message' => 'Cette commande ne peut plus être annulée.'],
                422,
            );
        }

        // Restore stock when cancelling an order
        DB::transaction(function () use ($order, $request) {
            $reason = $request->input('reason');
            $order->statut = 'annule';
            if ($reason) {
                $order->notes = trim(
                    ($order->notes ? $order->notes . "\n" : '') . 'Annulation : ' . $reason,
                );
            }
            $order->save();

            // Restore stock for each product in the cancelled order
            foreach ($order->produits as $produit) {
                $qty = (int) $produit->pivot->quantite;
                Produits::where('id', $produit->id)->increment('stock', $qty);
            }
        });

        $order->loadCount('produits')->load('produits.photos');

        return new OrderResource($order);
    }

    /**
     * Le client déclare avoir payé sa commande (mobile money manuel).
     * Crée une preuve de paiement en attente + notifie les admins.
     */
    public function pay(Request $request, $id)
    {
        $data = $request->validate([
            'payment_method_id' => 'nullable|integer|exists:paiements,id',
            'provider' => 'nullable|string|max:60',
            'phone' => 'nullable|string|max:30',
            'transaction_id' => 'nullable|string|max:120',
            'wallet' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $order = Commandes::where('user_id', $user->id)->findOrFail($id);

        // === Paiement direct avec le portefeuille (solde rechargé) ===
        if ($request->boolean('wallet')) {
            return $this->payWithWallet($user, $order);
        }

        $method = null;
        if (!empty($data['payment_method_id'])) {
            $method = Paiements::find($data['payment_method_id']);
        }
        $isCod = $method && $method->isCashOnDelivery();
        $providerLabel = $data['provider'] ?? ($method->method_name ?? 'Mobile Money');

        // === Paiement à la livraison : commande confirmée directement ===
        if ($isCod) {
            $order->paiement_id = $method->id ?? $order->paiement_id;
            $order->statut = 'payee';
            $order->date_traitement = now();
            $order->notes = trim(($order->notes ? $order->notes . "\n" : '')
                . 'Paiement à la livraison (espèces).');
            $order->save();

            PaymentProof::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'provider' => 'À la livraison',
                'amount' => $order->total,
                'status' => 'cod',
            ]);

            $numero = $order->numero_commande ?? ('#' . $order->id);
            AdminNotifier::notifyPayment(
                'Nouvelle commande (paiement à la livraison)',
                "{$user->name} a passé la commande {$numero} à payer à la livraison — "
                    . number_format((float) $order->total, 0, ',', ' ') . ' FCFA.',
                ['type' => 'admin_payment', 'id' => $order->id],
            );

            $order->loadCount('produits')->load('produits.photos');

            return (new OrderResource($order))->additional([
                'message' => 'Commande confirmée. Vous paierez à la livraison.',
            ]);
        }

        // === Paiement mobile money : déclaration à vérifier ===
        // Enregistre la méthode choisie sur la commande + statut : paiement déclaré
        $order->paiement_id = $method->id ?? $order->paiement_id;
        $order->statut = 'paiement_declare';
        $order->date_en_attente = now();
        $notePaiement = "Paiement déclaré via {$providerLabel}"
            . (!empty($data['phone']) ? " — {$data['phone']}" : '')
            . (!empty($data['transaction_id']) ? " — ref: {$data['transaction_id']}" : '');
        $order->notes = trim(($order->notes ? $order->notes . "\n" : '') . $notePaiement);
        $order->save();

        // Preuve de paiement (à confirmer par l'admin)
        PaymentProof::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'provider' => $providerLabel,
            'phone' => $data['phone'] ?? null,
            'amount' => $order->total,
            'status' => 'pending',
            'notes' => $data['transaction_id'] ?? null,
        ]);

        // Notifie les administrateurs (base + email)
        $numero = $order->numero_commande ?? ('#' . $order->id);
        AdminNotifier::notifyPayment(
            'Nouveau paiement à vérifier',
            "{$user->name} a déclaré avoir payé la commande {$numero} ({$providerLabel}) — "
                . number_format((float) $order->total, 0, ',', ' ') . ' FCFA.',
            ['type' => 'admin_payment', 'id' => $order->id],
        );

        $order->loadCount('produits')->load('produits.photos');

        return (new OrderResource($order))->additional([
            'message' => 'Paiement déclaré. En attente de confirmation par le vendeur.',
        ]);
    }

    /**
     * Règle une commande directement avec le solde du portefeuille.
     * Le paiement est confirmé immédiatement (pas de vérification vendeur).
     */
    private function payWithWallet(User $user, Commandes $order)
    {
        // On ne paie pas deux fois une commande déjà réglée/traitée.
        if (in_array($order->statut, ['payee', 'expedie', 'livre'], true)) {
            return response()->json(['message' => 'Cette commande est déjà réglée.'], 422);
        }

        $total = (float) $order->total;
        if ((float) ($user->wallet_balance ?? 0) < $total) {
            return response()->json([
                'message' => 'Solde du portefeuille insuffisant. Rechargez votre portefeuille.',
            ], 422);
        }

        DB::transaction(function () use ($user, $order, $total) {
            $payer = User::whereKey($user->id)->lockForUpdate()->first();
            if ((float) ($payer->wallet_balance ?? 0) < $total) {
                throw new \RuntimeException('Solde insuffisant.');
            }
            $payer->decrement('wallet_balance', $total);

            $order->statut = 'payee';
            $order->date_traitement = now();
            $order->notes = trim(($order->notes ? $order->notes . "\n" : '')
                . 'Payée avec le portefeuille.');
            $order->save();

            WalletTransaction::create([
                'user_id' => $payer->id,
                'type' => 'purchase',
                'amount' => $total,
                'method' => 'wallet',
                'status' => 'confirmed',
                'reference' => $order->numero_commande ?? ('CMD-' . $order->id),
                'note' => 'Paiement de la commande ' . ($order->numero_commande ?? ('#' . $order->id)),
            ]);

            PaymentProof::create([
                'user_id' => $payer->id,
                'order_id' => $order->id,
                'provider' => 'Portefeuille',
                'amount' => $total,
                'status' => 'confirme',
            ]);
        });

        // Informe l'administration qu'une commande a été payée via portefeuille.
        $numero = $order->numero_commande ?? ('#' . $order->id);
        AdminNotifier::notifyPayment(
            'Commande payée via portefeuille',
            "{$user->name} a payé la commande {$numero} avec son portefeuille — "
                . number_format($total, 0, ',', ' ') . ' FCFA.',
            ['type' => 'admin_payment', 'id' => $order->id],
        );

        $order->loadCount('produits')->load('produits.photos');

        return (new OrderResource($order))->additional([
            'message' => 'Commande payée avec votre portefeuille.',
        ]);
    }
}
