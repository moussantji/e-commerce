<?php

namespace App\Livewire\Frontend\Checkout;

use Livewire\Component;
use App\Models\Commandes;
use Illuminate\Support\Facades\DB;

class CheckoutSummary extends Component
{
    public $commande;
    public $itemsSubtotal = 0;
    public $discount = 0;
    public $tax = 0;
    public $shippingCost = 0;
    public $finalTotal = 0;
    // ✅ Livewire v3
    protected $listeners = ['shipping-changed' => 'handleShippingChange'];

    public function mount($commande)
    {
        $this->commande = $commande;
        $this->loadTotals();
    }

    public function handleShippingChange()
    {
        $this->commande->refresh();  // ✅ Sync DB
        $this->loadTotals();         // ✅ Recalcul
    }
    public function loadTotals()
    {
        // Items subtotal from commande_produit
        $this->itemsSubtotal = DB::table('commande_produit')
            ->where('commande_id', $this->commande->id)
            ->sum('total');

        // Commande fields
        $this->discount = $this->commande->remise ?? 0;
        $this->shippingCost = $this->commande->frais_livraison ?? 0;

        // Final calculation
        $this->finalTotal = $this->itemsSubtotal + $this->tax + $this->shippingCost - $this->discount;
    }

    public function render()
    {
        return view('livewire.frontend.checkout.checkout-summary');
    }

    public function payNow()
{
    // ✅ Validation finale
    $this->validate();

    // ✅ Lancer paiement (CinetPay, etc.)
    $this->dispatch('process-payment', [
        'commandeId' => $this->commande->id,
        'total' => $this->finalTotal
    ]);

    session()->flash('message', 'Paiement en cours...');
}

public function saveAndExit()
{
    // ✅ Marquer comme sauvegardé
    $this->commande->update(['statut' => 'en_attente']);

    session()->flash('success', 'Commande sauvegardée !');
    return $this->redirect(route('dashboard'));
}

}
