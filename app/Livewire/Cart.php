<?php

namespace App\Livewire;

use App\Models\Paniers;
use Livewire\Component;
use App\Models\Produits;
use App\Models\Commandes;
use App\Models\Livraison;
use App\Models\Paiements;
use App\Models\PromoCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{
    public $panier;
    public $cartItems = []; // ← NOUVEAU : tableau simple
    public $total = 0;
    public $itemsCount = 0;

    public $paymentMethods; // ✅ Vos vraies méthodes
    public $paymentMethodId; // ✅ ID sélectionné
    public $deliveryMethods;
    public $deliveryMethodId;
    public $voucherCode = '';
    public $voucherError = '';  // ✅ Property pour erreur
    public $discount = 0;
    public $taxRate = 0.18; // 18% TVA
    public $shippingCost = 0;


    protected $listeners = ['cartUpdated' => '$refresh'];

    public function mount()
    {
        // ✅ Récupère VOS méthodes de paiement existantes
        $this->paymentMethods = Paiements::where('is_active', true)->with('photos')->get();
        $this->paymentMethodId = $this->paymentMethods->first()?->id ?? null;
        $this->deliveryMethods = Livraison::where('is_active', true)->get();
        $this->deliveryMethodId = $this->deliveryMethods->first()?->id ?? null;
        $delivery = Livraison::find($this->deliveryMethodId);
        $this->shippingCost = $delivery->price ?? 5000;
        $this->loadCart();
    }

    public function loadCart()
    {
        if (!Auth::check()) {
            $this->panier = null;
            $this->itemsCount = 0;
            $this->total = 0;
            return;
        }

        $panierId = DB::table('paniers')
            ->where('user_id', Auth::user()->id)
            ->where('status', 'actif')
            ->value('id');

        if (!$panierId) {
            $this->panier = null;
            $this->itemsCount = 0;
            $this->total = 0;
            return;
        }

        // ✅ Récupère SEULEMENT les IDs avec données
        $productIds = DB::table('panier_produit')
            ->where('paniers_id', $panierId)
            ->pluck('produits_id');

        if ($productIds->isEmpty()) {
            $this->panier = null;
            $this->itemsCount = 0;
            $this->total = 0;
            return;
        }

        // ✅ Charge modèles + pivot SÉCURISÉ
        // Dans loadCart() - Remplacez la map par :
        $produits = Produits::with('photos')
            ->whereIn('id', $productIds)
            ->get()
            ->map(function ($produit) use ($panierId) {
                $pivot = DB::table('panier_produit')
                    ->where('paniers_id', $panierId)
                    ->where('produits_id', $produit->id)
                    ->first();

                // ✅ SÉCURITÉ renforcée
                if (!$pivot) {
                    return null;
                }

                // ✅ Vérifie TOUS les champs requis
                if (!isset($pivot->quantite, $pivot->prix_unitaire, $pivot->total_ligne)) {
                    return null;
                }

                $produit->pivot = (object) [
                    'quantite' => (int)($pivot->quantite ?? 1),
                    'prix_unitaire' => (float)($pivot->prix_unitaire ?? 0),
                    'total_ligne' => (float)($pivot->total_ligne ?? 0)
                ];

                return $produit;
            })
            ->filter()
            ->values();


        $this->panier = (object) [
            'id' => $panierId,
            'products' => $produits
        ];

        $this->itemsCount = $produits->sum(fn($p) => $p->pivot->quantite ?? 0);
        $this->total = $produits->sum(fn($p) => $p->pivot->total_ligne ?? 0);
        session(['cart_count' => $this->itemsCount]);
    }


    // ✅ MODIF JSON UNIQUEMENT
    public function updateQuantity($productId, $quantity)
    {
        if (!$this->panier || $quantity < 1) return;

        $contenu = collect($this->panier->contenu);
        $contenu = $contenu->map(function ($item) use ($productId, $quantity) {
            if ($item['produit_id'] == $productId) {
                $item['quantite'] = $quantity;
                $item['total_ligne'] = $item['prix_unitaire'] * $quantity;
            }
            return $item;
        });

        // ✅ 1 SEULE REQUÊTE
        DB::table('paniers')
            ->where('id', $this->panier->id)
            ->update(['contenu' => json_encode($contenu->values()->toArray())]);

        $this->loadCart();
    }

    public function decreaseQuantity($productId)
    {
        if (!$this->panier) return;

        $pivotRow = DB::table('panier_produit')
            ->where('paniers_id', $this->panier->id)
            ->where('produits_id', $productId)
            ->first();

        if (!$pivotRow || !isset($pivotRow->quantite) || $pivotRow->quantite <= 1) {
            $this->removeFromCart($productId);
            return;
        }

        // ✅ NOUVELLE QUANTITÉ + recalcul CORRECT
        $newQuantity = $pivotRow->quantite - 1;
        $newTotal = $pivotRow->prix_unitaire * $newQuantity;

        DB::table('panier_produit')
            ->where('paniers_id', $this->panier->id)
            ->where('produits_id', $productId)
            ->update([
                'quantite' => $newQuantity,
                'total_ligne' => $newTotal
            ]);
        session(['cart_count' => $this->itemsCount]);

        $this->loadCart();

        // 🔥 AUTO-REFRESH navbar
        $this->dispatch('refresh-navbar-cart');
    }


    public function removeFromCart($productId)
    {
        if (!$this->panier) return;

        // ✅ SUPPRESSION DIRECTE + VERIFICATION
        $deleted = DB::table('panier_produit')
            ->where('paniers_id', $this->panier->id)
            ->where('produits_id', $productId)
            ->delete();

        if ($deleted > 0) {
            session()->flash('message', 'Produit supprimé du panier !');
        }


        $this->loadCart();
        // 🔥 AUTO-REFRESH navbar
        $this->dispatch('refresh-navbar-cart');
    }

    public function increaseQuantity($productId)
    {
        if (!$this->panier) return;

        $pivotRow = DB::table('panier_produit')
            ->where('paniers_id', $this->panier->id)
            ->where('produits_id', $productId)
            ->first();

        if (!$pivotRow || !isset($pivotRow->quantite)) return;

        // ✅ NOUVELLE QUANTITÉ + recalcul CORRECT
        $newQuantity = $pivotRow->quantite + 1;
        $newTotal = $pivotRow->prix_unitaire * $newQuantity;

        DB::table('panier_produit')
            ->where('paniers_id', $this->panier->id)
            ->where('produits_id', $productId)
            ->update([
                'quantite' => $newQuantity,
                'total_ligne' => $newTotal
            ]);
        session(['cart_count' => $this->itemsCount]);

        $this->loadCart();
        // 🔥 AUTO-REFRESH navbar
        $this->dispatch('refresh-navbar-cart');
    }
    public function formatFcfa($amount)
    {
        return number_format($amount ?? 0, 0, ',', ' ') . ' FCFA';
    }

    public function updatedVoucherCode()
    {
        $this->applyVoucher();
    }

    public function applyVoucher()
    {
        if (!$this->total) {
            $this->voucherError = 'Panier vide !';
            $this->resetErrorBag();
            $this->discount = 0;  // 🔥 CLEAR !
            $this->loadCart();  // ✅ TOUJOURS appeler !
            return;
        }

        $promo = PromoCode::where('code', $this->voucherCode)
            ->where('is_active', true)
            ->first();
        if (!$promo) {  // ← NULL = pas trouvé
            $this->voucherError = 'Code non trouvé !';
            $this->discount = 0;  // 🔥 CLEAR !
            $this->loadCart();  // ✅ TOUJOURS appeler !
            return;
        }

        // ✅ MAINTENANT sûr d'appeler méthode
        if ($promo->isValid()) {
            $this->discount = $promo->calculateDiscount($this->total);
            $this->voucherError = '';
        } else {
            $this->voucherError = 'Code expiré !';
            $this->discount = 0;  // 🔥 CLEAR !
            $this->loadCart();  // ✅ TOUJOURS appeler !
        }

        $this->loadCart();
    }

    public function updatedDeliveryMethodId($deliveryId)
    {
        $delivery = Livraison::find($this->deliveryMethodId);
        $this->shippingCost = $delivery?->price ?? 5000;

        // ✅ AU CHARGEMENT aussi
        if (!$deliveryId) {
            $firstDelivery = Livraison::first();
            $this->shippingCost = $firstDelivery?->price ?? 5000;
        }
        $this->loadCart();
    }

    public function checkout()
    {
        if (!$this->panier || $this->itemsCount === 0) {
            session()->flash('error', 'Panier vide');
            return;
        }

        // Création commande + paiement/livraison
        $this->createOrder();
    }

    private function createOrder()
    {
        DB::transaction(function () {
            // 1. Créer commande depuis panier
            $order = Commandes::create([
                'user_id' => Auth::id(),
                'panier_id' => $this->panier->id,
                'total' => $this->getFinalTotal(),
                'status' => 'pending',
                'livraison_id' => $this->deliveryMethodId,
            ]);

            // 2. Créer paiement
            Paiements::create([
                'user_id' => Auth::id(),
                'amount' => $this->getFinalTotal(),
                'method_name' => $this->paymentMethod,
                'status' => 'pending',
                'payment_mode' => $this->paymentMethod,
                'description' => 'Paiement commande #' . $order->id
            ]);

            // 3. Vider panier
            DB::table('panier_produit')
                ->where('paniers_id', $this->panier->id)
                ->delete();

            $this->loadCart();
        });
    }

    public function getFinalTotal()
    {
        return $this->total - $this->discount + $this->shippingCost;
    }

    public function formatDeliveryTime($method)
    {
        $min = $method->delivery_time_min ?? 0;
        $max = $method->delivery_time_max ?? 0;
        $unit = $method->delivery_time_unit ?? 'hours';

        // ✅ 0-0 hours → "0"
        if ($min == 0 && $max == 0) {
            return null;
        }

        // 2-5 days → "2-5 jours"
        if ($min != $max) {
            return "( $min-$max $unit )";
        }

        // 3 hours → "3 hours"
        return "($min $unit )";
    }


    public function render()
    {
        return view('livewire.cart');
    }
}
