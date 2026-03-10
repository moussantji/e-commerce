<?php

namespace App\Livewire\Frontend\Checkout;

use App\Models\Livraison;
use App\Models\Commandes;
use Livewire\Component;

class ShippingOptions extends Component
{
    public $commande;
    public $livraisons = [];
    public $deliveryMethodId;

    public function mount($commande)
    {
        $this->commande = $commande;
        $this->deliveryMethodId = $commande->livraison_id ?? 4; // Default
        $this->loadLivraisons();
    }

    public function updatedDeliveryMethodId($value)
    {
        // ✅ Récupérer la livraison sélectionnée
        $livraison = Livraison::find($value);

        if ($livraison) {
            // ✅ Update commande LIVRAISON_ID ET FRAIS_LIVRAISON en même temps
            $this->commande->update([
                'livraison_id' => $value,
                'frais_livraison' => $livraison->price
            ]);
        }

        // ✅ Emit vers CheckoutSummary pour recalculer tous les totaux
        $this->dispatch('shipping-changed', [
            'shippingCost' => $livraison->price ?? 0,
            'commandeId' => $this->commande->id
        ]);
    }


    private function loadLivraisons()
    {
        $this->livraisons = Livraison::where('is_active', true)->get();
    }

    public function render()
    {
        return view('livewire.frontend.checkout.shipping-options');
    }
}
