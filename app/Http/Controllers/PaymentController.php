<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commandes;
use App\Models\Paiements;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Storage;
use App\Services\FedapayService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['storeManual']);
        $this->middleware(['auth', 'role:admin'])->only(['confirmPayment']);
    }

    /**
     * Store manual mobile-money payment proof from customer
     * Expected inputs: order_id, provider (orange|malitel|wave), phone (optional), photos[]
     */
    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:commandes,id',
            'provider' => 'required|in:orange,malitel,wave',
            'phone' => 'nullable|string',
            'photos.*' => 'nullable|image|max:5120'
        ]);

        $order = Commandes::findOrFail($data['order_id']);

        // vérifier que l'utilisateur est propriétaire de la commande
        if ($order->user_id !== auth()->id()) {
            return back()->with('error', 'Commande non autorisée.');
        }


        // Create a PaymentProof entry (separate from payment methods)
        $proofData = [
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'provider' => $data['provider'],
            'phone' => $data['phone'] ?? null,
            'amount' => $order->total ?? 0,
            'status' => 'pending',
        ];

        $photosPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                if (! $file->isValid()) continue;
                $path = $file->store('payment_proofs/' . auth()->id() . '/' . $order->id, 'public');
                $photosPaths[] = $path;
            }
        }

        if (count($photosPaths) > 0) {
            $proofData['photos'] = $photosPaths;
        }

        $proof = PaymentProof::create($proofData);

        // Lier la preuve à la commande (optionnel : garder référence dans commande)
        $order->statut = 'en_attente';
        $order->date_en_attente = now();
        $order->save();

        return redirect()->route('commande.show', $order->id)->with('success', "Preuve envoyée — en attente de confirmation par l'admin.");
    }

    /**
     * Admin confirms the payment for an order
     */
    public function confirmPayment(Request $request, Commandes $order)
    {
        $paiement = $order->paiement;
        if (! $paiement) {
            return back()->with('error', 'Aucun paiement associé à cette commande.');
        }

        $paiement->status = 'confirme';
        $paiement->payment_date = now();
        $paiement->save();

        $order->statut = 'payee';
        $order->date_traitement = now();
        $order->save();

        return back()->with('success', 'Paiement confirmé et commande marquée comme payée.');
    }

    /**
     * Initiate Fedapay payment for an order and redirect customer to Fedapay checkout
     * expects query: order_id, provider
     */
    public function initiateFedapay(Request $request)
    {
        Log::info('PaymentController::initiateFedapay called', $request->all());

        $data = $request->validate([
            'order_id' => 'required|exists:commandes,id',
            'provider' => 'required|string'
        ]);

        $order = Commandes::findOrFail($data['order_id']);

        $returnUrl = URL::route('paiement.success');
        $notifyUrl = URL::route('paiement.fedapay.callback');

        $service = new FedapayService();
        $customer = [
            'name' => $order->user?->name ?? 'Client',
            'email' => $order->user?->email ?? null,
            'phone' => $order->user?->tel ?? null,
        ];

        $res = $service->createPayment(
            $order->total,
            config('app.currency', 'XOF'),
            strtolower($data['provider']),
            $customer,
            'CMD-' . $order->id,
            $returnUrl,
            $notifyUrl
        );

        Log::info('Fedapay createPayment response', ['order' => $order->id, 'provider' => $data['provider'], 'response' => $res]);

        if ($res['ok'] && $res['url']) {
            // store remote payment id if available
            if (isset($res['response']['data']['id'])) {
                $order->cinetpay_transaction_id = $res['response']['data']['id'];
                $order->save();
            }
            return redirect()->away($res['url']);
        }

        return redirect()->route('commande.show', $order->id)->with('error', 'Impossible d initier le paiement.');
    }

    /**
     * Handle Fedapay webhook/notifications
     */
    public function notifyFedapay(Request $request)
    {
        $payload = $request->all();

        // Try to extract merchant order ref (format CMD-<id>)
        $orderId = null;
        if (isset($payload['merchant']['order_ref'])) {
            $ref = $payload['merchant']['order_ref'];
            if (preg_match('/CMD-(\d+)/', $ref, $m)) {
                $orderId = (int)$m[1];
            }
        }

        // Fallback: try data->merchant->order_ref
        if (! $orderId && isset($payload['data']['merchant']['order_ref'])) {
            $ref = $payload['data']['merchant']['order_ref'];
            if (preg_match('/CMD-(\d+)/', $ref, $m)) {
                $orderId = (int)$m[1];
            }
        }

        if ($orderId) {
            $order = Commandes::find($orderId);
            if ($order) {
                // Mark as paid if payment status indicates success
                $status = $payload['status'] ?? ($payload['data']['status'] ?? null);
                if (in_array(strtolower($status), ['paid', 'success', 'completed'])) {
                    $order->statut = 'payee';
                    $order->date_traitement = now();
                    $order->save();
                }
            }
        }

        return response('OK', 200);
    }
}
