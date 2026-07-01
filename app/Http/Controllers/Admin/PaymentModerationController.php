<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Modération des paiements côté admin web :
 *  - Confirmer / rejeter les paiements de commandes (PaymentProof)
 *  - Confirmer / rejeter les rechargements de portefeuille
 */
class PaymentModerationController extends Controller
{
    public function index()
    {
        $payments = PaymentProof::with(['user', 'order'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $topups = collect();
        if (Schema::hasTable('wallet_transactions')) {
            $topups = WalletTransaction::with('user')
                ->where('type', 'topup')
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        return view('admin.payments.index', compact('payments', 'topups'));
    }

    public function confirmPayment(PaymentProof $proof)
    {
        DB::transaction(function () use ($proof) {
            $proof->update(['status' => \App\Support\PaymentStatus::CONFIRMED]);
            if ($proof->order) {
                // déclenche la notif de changement de statut (hook Commandes)
                $proof->order->update(['statut' => 'payee', 'date_traitement' => now()]);
            }
        });

        return back()->with('success', 'Paiement confirmé, commande marquée comme payée.');
    }

    public function rejectPayment(Request $request, PaymentProof $proof)
    {
        $reason = $request->input('reason');
        $proof->update(['status' => \App\Support\PaymentStatus::REJECTED]);

        if ($proof->order) {
            $proof->order->update(['statut' => 'en_attente']);
        }

        if ($proof->user) {
            $numero = optional($proof->order)->numero_commande ?? ('#' . $proof->order_id);
            $proof->user->notify(new PaymentStatusNotification(
                'Paiement non validé',
                "Votre paiement pour la commande {$numero} n'a pas pu être validé."
                    . ($reason ? " Motif : {$reason}" : ''),
                false,
                ['type' => 'order', 'id' => $proof->order_id],
            ));
        }

        return back()->with('success', 'Paiement rejeté.');
    }

    public function confirmTopup(WalletTransaction $transaction)
    {
        if ($transaction->type !== 'topup') {
            return back()->with('error', 'Transaction invalide.');
        }
        if ($transaction->status === 'confirmed') {
            return back()->with('success', 'Déjà confirmé.');
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update(['status' => 'confirmed']);
            User::whereKey($transaction->user_id)->lockForUpdate()->first()
                ?->increment('wallet_balance', (float) $transaction->amount);
        });

        $transaction->user?->notify(new PaymentStatusNotification(
            'Rechargement confirmé',
            'Votre portefeuille a été crédité de '
                . number_format((float) $transaction->amount, 0, ',', ' ') . ' FCFA.',
            true,
            ['type' => 'wallet'],
        ));

        return back()->with('success', 'Rechargement confirmé et crédité.');
    }

    public function rejectTopup(WalletTransaction $transaction)
    {
        if ($transaction->type !== 'topup') {
            return back()->with('error', 'Transaction invalide.');
        }
        $transaction->update(['status' => 'rejected']);

        $transaction->user?->notify(new PaymentStatusNotification(
            'Rechargement non validé',
            'Votre rechargement de ' . number_format((float) $transaction->amount, 0, ',', ' ')
                . " FCFA n'a pas pu être validé.",
            false,
            ['type' => 'wallet'],
        ));

        return back()->with('success', 'Rechargement rejeté.');
    }
}
