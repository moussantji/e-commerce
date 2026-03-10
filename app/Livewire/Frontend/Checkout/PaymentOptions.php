<?php

namespace App\Livewire\Frontend\Checkout;

use App\Models\Paiements;
use App\Models\Commandes;
use Livewire\Component;

class PaymentOptions extends Component
{
    public $paymentMethodId;
    public $paymentMethods;
    public $commande;
    public $selectedMethod;

    public function mount($commande)
    {
        $this->commande = $commande;
        $this->paymentMethodId = $commande->paiement_id ?? 1; // Default Credit Card
        $this->loadPaymentMethods();
        $this->updatedPaymentMethodId($this->paymentMethodId);
    }

    public function updatedPaymentMethodId($value)
    {
        $this->selectedMethod = $this->paymentMethods->find($value);

        // Update commande in real-time
        if ($this->commande && $this->commande->exists) {
            $this->commande->update(['paiement_id' => $value]);
        }

        $this->dispatch('payment-changed', [
            'paymentMethodId' => $value,
            'methodName' => $this->selectedMethod?->method_name ?? ''
        ]);
    }

    private function loadPaymentMethods()
    {
        $this->paymentMethods = Paiements::where('is_active', true ?? 1)
            ->orderBy('id')->get();
    }

    public function render()
    {
        return view('livewire.frontend.checkout.payment-options');
    }
}
