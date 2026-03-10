<?php

namespace App\Livewire\Frontend\Checkout;

use Livewire\Component;
use App\Models\Commandes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutActions extends Component
{
    public $commande;
    public $finalTotal;
    public $itemsSubtotal = 0;
    public $discount = 0;
    public $tax = 0;
    public $shippingCost = 0;

    protected $listeners = ['shipping-changed' => 'handleShippingChange'];


    public function mount($commande)
    {
        $this->commande = $commande;
        $this->finalTotal = $commande->total;
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
    public function payNow()
    {
        // ✅ Marque "en paiement"
        $this->commande->update(['statut' => 'en_attente']);

        // ✅ WhatsApp
        $whatsappUrl = $this->generateWhatsAppUrl();
        return redirect()->away($whatsappUrl);
    }

    public function saveAndExit()
    {
        $this->commande->update(['status' => 'en_attente']);
        session()->flash('success', 'Commande sauvegardée !');
        return $this->redirect(route('dashboard'));
    }

    private function generateWhatsAppUrl()
    {
        $whatsappNumber = config('app.whatsapp_number');

        $message = "🛒 *COMMANDE #{$this->commande->numero_commande}*\n\n" .
            "👤 " . Auth::user()->full_name . "\n📞 " . Auth::user()->tel . "\n\n" .
            "💰 *TOTAL :* " . number_format($this->finalTotal, 0) . " FCFA\n" .
            "🚚 " . $this->commande->livraison->method_name . "\n" .
            "💳 " . $this->commande->paiement->method_name;

        // ✅ Format MALI : 223 + numéro .env
        return "https://api.whatsapp.com/send?phone=223{$whatsappNumber}&text=" .
            rawurlencode($message);
    }


    public function render()
    {
        return view('livewire.frontend.checkout.checkout-actions');
    }
}
