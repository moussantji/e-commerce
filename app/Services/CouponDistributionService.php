<?php

namespace App\Services;

use App\Models\Commandes;
use App\Models\PromoCode;
use App\Models\User;
use App\Notifications\CouponAssignedNotification;
use App\Support\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Règles métier de distribution des coupons.
 *
 * - Commande : application directe dès 10 articles ou plus de 100 000 FCFA.
 * - Cumul mensuel : attribution d'un coupon au-delà de 20 articles ou à
 *   partir de 200 000 FCFA de commandes payées dans le mois.
 *
 * Seul le coupon marqué « automatique » dans l'admin est utilisé. Il n'est
 * jamais affiché sur la boutique : il est appliqué au panier ou envoyé au
 * client par notification + email.
 */
class CouponDistributionService
{
    /** Coupon actif désigné pour la distribution automatique. */
    public function automaticCoupon(): ?PromoCode
    {
        return PromoCode::query()
            ->automatic()
            ->where('is_active', true)
            ->latest('id')
            ->get()
            ->first(fn (PromoCode $coupon) => $coupon->isValid());
    }

    /** Une commande déclenche-t-elle l'application directe du coupon ? */
    public function orderQualifies(float $subtotal, int $quantity): bool
    {
        return $quantity >= (int) config('shop.coupon_order_min_quantity', 10)
            || $subtotal > (float) config('shop.coupon_order_min_subtotal', 100000);
    }

    /** Coupon à appliquer automatiquement à une commande, si éligible. */
    public function automaticCouponForOrder(float $subtotal, int $quantity): ?PromoCode
    {
        if (!$this->orderQualifies($subtotal, $quantity)) {
            return null;
        }

        return $this->automaticCoupon();
    }

    /**
     * Quantité totale et valeur des commandes payées du mois en cours.
     *
     * @return array{quantity: int, total: float}
     */
    public function monthlyStats(User $user): array
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $paidStatuses = [OrderStatus::PAYEE, OrderStatus::EXPEDIE, OrderStatus::LIVRE];

        $quantity = DB::table('commande_produit')
            ->join('commandes', 'commandes.id', '=', 'commande_produit.commande_id')
            ->where('commandes.user_id', $user->id)
            ->whereIn('commandes.statut', $paidStatuses)
            ->whereBetween('commandes.created_at', [$start, $end])
            ->sum('commande_produit.quantite');

        $total = DB::table('commandes')
            ->where('user_id', $user->id)
            ->whereIn('statut', $paidStatuses)
            ->whereBetween('created_at', [$start, $end])
            ->sum('total');

        return [
            'quantity' => (int) $quantity,
            'total' => (float) $total,
        ];
    }

    /** Le client atteint-il un seuil mensuel d'attribution ? */
    public function monthlyQualifies(User $user): bool
    {
        $stats = $this->monthlyStats($user);

        return $stats['quantity'] > (int) config('shop.coupon_month_min_quantity', 20)
            || $stats['total'] >= (float) config('shop.coupon_month_min_total', 200000);
    }

    /**
     * À appeler lorsqu'une commande passe au statut payé.
     * Attribue le coupon mensuel une fois par client, par coupon et par mois.
     */
    public function handlePaidOrder(Commandes $order): void
    {
        if (!$order->user || !in_array(OrderStatus::normalize($order->statut), [
            OrderStatus::PAYEE,
            OrderStatus::EXPEDIE,
            OrderStatus::LIVRE,
        ], true)) {
            return;
        }

        $coupon = $this->automaticCoupon();
        if (!$coupon || !$this->monthlyQualifies($order->user)) {
            return;
        }

        $this->send($order->user, $coupon, 'monthly');
    }

    /**
     * Attribue un coupon à un client et lui envoie notification + email.
     * Retourne true uniquement si le coupon vient d'être attribué/envoyé.
     */
    public function send(User $user, PromoCode $coupon, string $source = 'manual'): bool
    {
        if (!$coupon->isValid()) {
            return false;
        }

        $assignment = $coupon->users()->where('users.id', $user->id)->first();
        $currentMonth = now()->format('Y-m');
        $lastSentMonth = $assignment?->pivot?->last_sent_month;

        // Compatibilité avec les attributions créées avant l'ajout de
        // last_sent_month : on se base alors sur la date d'envoi.
        if (!$lastSentMonth && $assignment?->pivot?->sent_at) {
            $lastSentMonth = \Illuminate\Support\Carbon::parse($assignment->pivot->sent_at)->format('Y-m');
        }

        // Un seul envoi par mois : le coupon peut être renvoyé le mois suivant.
        if ($lastSentMonth === $currentMonth) {
            return false;
        }

        $payload = [
            'sent_at' => now(),
            'source' => $source,
            'last_sent_month' => $currentMonth,
        ];

        if ($assignment) {
            // Attribution existante (ex. coupon appliqué au panier) : on
            // complète la ligne sans la dupliquer.
            $coupon->users()->updateExistingPivot($user->id, $payload);
        } else {
            $coupon->users()->attach($user->id, $payload);
        }

        try {
            $user->notify(new CouponAssignedNotification($coupon, $source));
        } catch (\Throwable $e) {
            Log::warning('Notification coupon échouée : ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Enregistre l'utilisation d'un coupon sur une commande (sans email).
     * Crée aussi l'attribution si le coupon a été appliqué automatiquement.
     */
    public function recordUsage(User $user, PromoCode $coupon, string $source = 'auto_order'): void
    {
        $assignment = $coupon->users()->where('users.id', $user->id)->first();
        $currentMonth = now()->format('Y-m');

        if ($assignment) {
            $payload = [
                'used_at' => now(),
                'source' => $assignment->pivot->source ?: $source,
            ];

            // Une utilisation vaut attribution pour le mois en cours : évite
            // d'envoyer un second email identique au même client ce mois-ci.
            if (!$assignment->pivot->last_sent_month) {
                $payload['last_sent_month'] = $currentMonth;
            }

            if (!$assignment->pivot->sent_at) {
                $payload['sent_at'] = now();
            }

            $coupon->users()->updateExistingPivot($user->id, $payload);

            return;
        }

        $coupon->users()->attach($user->id, [
            'sent_at' => now(),
            'used_at' => now(),
            'source' => $source,
            'last_sent_month' => $currentMonth,
        ]);
    }

    /**
     * Envoie le coupon à tous les clients qui atteignent déjà les seuils
     * mensuels. Utile après la création d'un coupon automatique.
     */
    public function distributeToEligibleUsers(PromoCode $coupon): int
    {
        $sent = 0;

        User::query()
            ->whereHas('orders')
            ->chunkById(200, function ($users) use ($coupon, &$sent) {
                foreach ($users as $user) {
                    if ($this->monthlyQualifies($user) && $this->send($user, $coupon, 'monthly')) {
                        $sent++;
                    }
                }
            });

        return $sent;
    }
}
