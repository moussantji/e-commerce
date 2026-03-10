<?php

namespace App\Http\Controllers;

use App\Models\Commandes;
use Illuminate\Http\Request;
use App\Services\CinetPayService;

class CheckoutController extends Controller
{
    // ✅ 1. INIT PAIEMENT (Client clique "Payer")
    public function process(Request $request)
    {
        $commande = Commandes::create([
            'user_id' => auth()->id(),
            'total' => session('checkout_subtotal') + 15000,
            'status' => 'pending'
        ]);

        $cinetpay = new CinetPayService();
        $paiement = $cinetpay->payer(
            $commande->total,
            auth()->user()->name,
            auth()->user()->telephone,
            'CMD #' . $commande->id
        );

        if ($paiement['ok']) {
            session(['cinetpay_id' => $commande->id]);
            return redirect($paiement['url']); // → Page CinetPay
        }
        return back()->with('error', 'Erreur paiement');
    }

    // ✅ 2. RETURN (CinetPay redirige APRÈS paiement)
    public function return(Request $request)
    {
        $transaction_id = $request->transaction_id;
        $cinetpay = new CinetPayService();

        if ($cinetpay->verifier($transaction_id)) {
            $commande = Commandes::where('id', session('cinetpay_id'))->first();
            $commande->update(['status' => 'paid']);
            session()->forget('cinetpay_id');
            return redirect()->route('checkout.success')->with('success', 'Paiement OK');
        }
        return redirect()->route('checkout.failed')->with('error', 'Paiement échoué');
    }

    // ✅ 3. NOTIFY (CinetPay NOTIFIE en arrière-plan - CRITIQUE)
    public function notify(Request $request)
    {
        $transaction_id = $request->transaction_id;
        $cinetpay = new CinetPayService();

        if ($cinetpay->verifier($transaction_id)) {
            $commande = Commandes::where('transaction_id', $transaction_id)->first();
            if ($commande) {
                $commande->update(['status' => 'paid']);
            }
        }
        // Retour vide pour CinetPay
        return response('OK', 200);
    }

    // ✅ 4. CANCEL (Client annule)
    public function cancel(Request $request)
    {
        return redirect()->route('checkout.failed')->with('error', 'Paiement annulé');
    }
}
