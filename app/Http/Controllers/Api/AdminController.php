<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commandes;
use App\Models\PaymentProof;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Espace admin (mobile) : modération des paiements & rechargements.
 * Toutes les méthodes exigent un utilisateur avec role = admin.
 */
class AdminController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless(optional($request->user())->role === 'admin', 403, 'Accès réservé aux administrateurs.');
    }

    /** Tableau de bord synthétique. */
    public function summary(Request $request)
    {
        $this->ensureAdmin($request);

        return response()->json([
            'pending_payments' => PaymentProof::where('status', 'pending')->count(),
            'pending_topups' => WalletTransaction::where('type', 'topup')->where('status', 'pending')->count(),
            'orders_today' => Commandes::whereDate('created_at', today())->count(),
        ]);
    }

    /** Paiements de commandes en attente de vérification. */
    public function payments(Request $request)
    {
        $this->ensureAdmin($request);

        $proofs = PaymentProof::with(['user', 'order'])
            ->where('status', 'pending')
            ->latest()
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'data' => $proofs->getCollection()->map(fn ($p) => [
                'id' => $p->id,
                'order_id' => $p->order_id,
                'numero' => optional($p->order)->numero_commande ?? ('#' . $p->order_id),
                'client' => optional($p->user)->name ?? 'Client',
                'provider' => $p->provider,
                'phone' => $p->phone,
                'amount' => (float) $p->amount,
                'reference' => $p->notes,
                'status' => $p->status,
                'date' => optional($p->created_at)->diffForHumans(),
            ]),
            'meta' => ['current_page' => $proofs->currentPage(), 'last_page' => $proofs->lastPage(), 'total' => $proofs->total()],
        ]);
    }

    /** Confirme un paiement de commande. */
    public function confirmPayment(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $proof = PaymentProof::with(['order', 'user'])->findOrFail($id);

        DB::transaction(function () use ($proof) {
            $proof->update(['status' => 'confirme']);
            if ($proof->order) {
                // Le changement de statut déclenche la notification (hook modèle Commandes)
                $proof->order->update(['statut' => 'payee', 'date_traitement' => now()]);
            }
        });

        return response()->json(['message' => 'Paiement confirmé.']);
    }

    /** Rejette un paiement de commande. */
    public function rejectPayment(Request $request, $id)
    {
        $this->ensureAdmin($request);
        $reason = $request->input('reason');

        $proof = PaymentProof::with(['order', 'user'])->findOrFail($id);
        $proof->update(['status' => 'rejete']);

        // La commande repasse en attente de paiement (le client peut re-payer)
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

        return response()->json(['message' => 'Paiement rejeté.']);
    }

    /** Rechargements de portefeuille en attente. */
    public function topups(Request $request)
    {
        $this->ensureAdmin($request);

        $topups = WalletTransaction::with('user')
            ->where('type', 'topup')
            ->where('status', 'pending')
            ->latest()
            ->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'data' => $topups->getCollection()->map(fn ($t) => [
                'id' => $t->id,
                'client' => optional($t->user)->name ?? 'Client',
                'amount' => (float) $t->amount,
                'method' => $t->method,
                'phone' => $t->phone,
                'reference' => $t->reference,
                'status' => $t->status,
                'date' => optional($t->created_at)->diffForHumans(),
            ]),
            'meta' => ['current_page' => $topups->currentPage(), 'last_page' => $topups->lastPage(), 'total' => $topups->total()],
        ]);
    }

    /** Confirme un rechargement : crédite le solde. */
    public function confirmTopup(Request $request, $id)
    {
        $this->ensureAdmin($request);

        $tx = WalletTransaction::with('user')->where('type', 'topup')->findOrFail($id);
        if ($tx->status === 'confirmed') {
            return response()->json(['message' => 'Déjà confirmé.']);
        }

        DB::transaction(function () use ($tx) {
            $tx->update(['status' => 'confirmed']);
            User::whereKey($tx->user_id)->lockForUpdate()->first()?->increment('wallet_balance', (float) $tx->amount);
        });

        $tx->user?->notify(new PaymentStatusNotification(
            'Rechargement confirmé',
            'Votre portefeuille a été crédité de '
                . number_format((float) $tx->amount, 0, ',', ' ') . ' FCFA.',
            true,
            ['type' => 'wallet'],
        ));

        return response()->json(['message' => 'Rechargement confirmé et crédité.']);
    }

    /** Rejette un rechargement. */
    public function rejectTopup(Request $request, $id)
    {
        $this->ensureAdmin($request);
        $reason = $request->input('reason');

        $tx = WalletTransaction::with('user')->where('type', 'topup')->findOrFail($id);
        $tx->update(['status' => 'rejected']);

        $tx->user?->notify(new PaymentStatusNotification(
            'Rechargement non validé',
            "Votre rechargement de " . number_format((float) $tx->amount, 0, ',', ' ')
                . ' FCFA n\'a pas pu être validé.' . ($reason ? " Motif : {$reason}" : ''),
            false,
            ['type' => 'wallet'],
        ));

        return response()->json(['message' => 'Rechargement rejeté.']);
    }
}
