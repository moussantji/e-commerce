<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Support\AdminNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tx = WalletTransaction::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn ($t) => $this->format($t, $user->id));

        return response()->json([
            'balance' => (float) ($user->wallet_balance ?? 0),
            'currency' => 'FCFA',
            'transactions' => $tx,
        ]);
    }

    /**
     * Rechargement : le client déclare avoir payé (mobile money manuel).
     * Crée une transaction "pending" + notifie les admins pour confirmation.
     */
    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
            'method' => 'required|string|max:60',
            'phone' => 'nullable|string|max:30',
            'transaction_id' => 'nullable|string|max:120',
        ]);

        $user = $request->user();

        $tx = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => $data['amount'],
            'method' => $data['method'],
            'phone' => $data['phone'] ?? null,
            'status' => 'pending',
            'reference' => $data['transaction_id'] ?? ('TOPUP-' . strtoupper(Str::random(6))),
            'note' => 'Rechargement déclaré, en attente de confirmation.',
        ]);

        AdminNotifier::notifyPayment(
            'Rechargement de portefeuille à vérifier',
            "{$user->name} a déclaré un rechargement de "
                . number_format((float) $data['amount'], 0, ',', ' ') . " FCFA ({$data['method']}).",
            ['type' => 'admin_wallet', 'id' => $tx->id],
        );

        return response()->json([
            'message' => 'Rechargement déclaré. En attente de confirmation par le vendeur.',
            'transaction' => $this->format($tx, $user->id),
        ], 201);
    }

    /**
     * Transfert vers un autre utilisateur (débité immédiatement du solde).
     */
    public function transfer(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:100',
            'recipient' => 'required|string|max:255', // email ou téléphone
            'note' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $amount = (float) $data['amount'];

        $recipient = User::where('email', $data['recipient'])
            ->orWhere('tel', $data['recipient'])
            ->first();

        if (!$recipient) {
            return response()->json(['message' => 'Destinataire introuvable.'], 422);
        }
        if ($recipient->id === $user->id) {
            return response()->json(['message' => 'Vous ne pouvez pas vous transférer à vous-même.'], 422);
        }
        if ((float) ($user->wallet_balance ?? 0) < $amount) {
            return response()->json(['message' => 'Solde insuffisant.'], 422);
        }

        DB::transaction(function () use ($user, $recipient, $amount, $data) {
            // Débit expéditeur
            $sender = User::whereKey($user->id)->lockForUpdate()->first();
            if ((float) ($sender->wallet_balance ?? 0) < $amount) {
                throw new \RuntimeException('Solde insuffisant.');
            }
            $sender->decrement('wallet_balance', $amount);
            $recip = User::whereKey($recipient->id)->lockForUpdate()->first();
            $recip->increment('wallet_balance', $amount);

            $ref = 'TRF-' . strtoupper(Str::random(6));
            WalletTransaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer_out',
                'amount' => $amount,
                'method' => 'wallet',
                'recipient_id' => $recip->id,
                'status' => 'confirmed',
                'reference' => $ref,
                'note' => $data['note'] ?? "Transfert vers {$recip->name}",
            ]);
            WalletTransaction::create([
                'user_id' => $recip->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'method' => 'wallet',
                'recipient_id' => $sender->id,
                'status' => 'confirmed',
                'reference' => $ref,
                'note' => $data['note'] ?? "Transfert reçu de {$sender->name}",
            ]);
        });

        return response()->json([
            'message' => 'Transfert effectué.',
            'balance' => (float) $user->fresh()->wallet_balance,
        ]);
    }

    private function format(WalletTransaction $t, int $userId): array
    {
        // Sens du montant du point de vue de l'utilisateur courant
        $incoming = in_array($t->type, ['topup', 'transfer_in', 'refund'], true)
            || ($t->type === 'transfer_out' && $t->recipient_id === $userId);

        $labels = [
            'topup' => 'Rechargement',
            'transfer_out' => 'Transfert envoyé',
            'transfer_in' => 'Transfert reçu',
            'purchase' => 'Achat',
            'refund' => 'Remboursement',
        ];

        return [
            'id' => $t->id,
            'type' => $t->type,
            'label' => $labels[$t->type] ?? $t->type,
            'amount' => (float) $t->amount,
            'incoming' => $incoming,
            'method' => $t->method,
            'status' => $t->status, // pending | confirmed | rejected
            'status_label' => $t->status_label,
            'status_color' => \App\Support\PaymentStatus::color($t->status),
            'reference' => $t->reference,
            'note' => $t->note,
            'date' => optional($t->created_at)->diffForHumans(),
        ];
    }
}
