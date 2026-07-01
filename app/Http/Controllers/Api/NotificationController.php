<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commandes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * API Notifications pour l'app mobile.
 *
 * Sources combinées (sans migration obligatoire) :
 *  1. Les notifications persistées Laravel (table `notifications`) si elle existe.
 *  2. Des notifications dérivées des commandes récentes de l'utilisateur.
 *  3. Un message de bienvenue.
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $items = $this->buildNotifications($request->user());

        return response()->json([
            'data' => array_values($items),
            'unread_count' => collect($items)->where('unread', true)->count(),
        ]);
    }

    public function unreadCount(Request $request)
    {
        $items = $this->buildNotifications($request->user());

        return response()->json([
            'unread_count' => collect($items)->where('unread', true)->count(),
        ]);
    }

    public function markAllRead(Request $request)
    {
        if (Schema::hasTable('notifications')) {
            $request->user()->unreadNotifications->markAsRead();
        }

        return response()->json(['message' => 'Notifications marquées comme lues.']);
    }

    private function buildNotifications($user): array
    {
        $items = [];

        // 1) Notifications persistées (système natif de Laravel)
        if (Schema::hasTable('notifications')) {
            foreach ($user->notifications()->latest()->take(20)->get() as $n) {
                $data = $n->data ?? [];
                $items[] = [
                    'id' => (string) $n->id,
                    'title' => $data['title'] ?? 'Notification',
                    'body' => $data['body'] ?? ($data['message'] ?? ''),
                    'time' => optional($n->created_at)->diffForHumans(),
                    'unread' => is_null($n->read_at),
                    'icon' => $data['icon'] ?? 'notifications-outline',
                    'link' => $this->linkFromData($data),
                ];
            }
        }

        // 2) Notifications dérivées des commandes récentes
        $orders = Commandes::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        foreach ($orders as $order) {
            [$title, $icon] = $this->orderMeta($order->statut);
            $numero = $order->numero_commande ?? ('#' . $order->id);
            $total = number_format((float) $order->total, 0, ',', ' ');

            $items[] = [
                'id' => 'order-' . $order->id,
                'title' => $title,
                'body' => "Commande {$numero} — {$total} FCFA",
                'time' => optional($order->created_at)->diffForHumans(),
                'unread' => in_array($order->statut, ['en_attente', 'traitement', 'expedie'], true),
                'icon' => $icon,
                'link' => ['type' => 'order', 'id' => $order->id],
            ];
        }

        // 3) Message de bienvenue
        $firstName = $user->name ? explode(' ', trim($user->name))[0] : '';
        $items[] = [
            'id' => 'welcome',
            'title' => trim("Bienvenue {$firstName}") . ' 🎉',
            'body' => 'Profitez de la livraison offerte dès 25 000 FCFA sur votre première commande.',
            'time' => optional($user->created_at)->diffForHumans(),
            'unread' => false,
            'icon' => 'gift-outline',
            'link' => ['type' => 'home'],
        ];

        return $items;
    }

    /** Détermine la cible de redirection d'une notification persistée. */
    private function linkFromData(array $data): array
    {
        // Lien déjà structuré (notifs récentes : order / payment / home / wallet…)
        if (isset($data['link']) && is_array($data['link']) && ! empty($data['link']['type'])) {
            return $data['link'];
        }
        if (!empty($data['product_id'])) {
            return ['type' => 'product', 'id' => (int) $data['product_id']];
        }
        if (!empty($data['order_id'])) {
            return ['type' => 'order', 'id' => (int) $data['order_id']];
        }
        // Notification liée au panier
        if (($data['icon'] ?? '') === 'cart-outline') {
            return ['type' => 'cart'];
        }
        return ['type' => 'none'];
    }

    private function orderMeta(?string $statut): array
    {
        return match ($statut) {
            'en_attente' => ['Commande en attente de paiement 💳', 'card-outline'],
            'traitement' => ['Commande en préparation 📦', 'cube-outline'],
            'expedie' => ['Votre commande est expédiée 🚚', 'car-outline'],
            'livre' => ['Commande livrée ✅', 'checkmark-done-outline'],
            'annule' => ['Commande annulée', 'close-circle-outline'],
            default => ['Mise à jour de commande', 'receipt-outline'],
        };
    }
}
