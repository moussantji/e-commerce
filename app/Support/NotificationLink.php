<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Résout l'URL et le texte web d'une notification à partir de sa charge `data`.
 *
 * Gère les deux formats présents en base :
 *   - ancien (web)     : ['url' => ..., 'message' => ..., 'user_name' => ...]
 *   - nouveau (mobile) : ['title' => ..., 'body' => ..., 'link' => ['type' => 'order', 'id' => 42]]
 *
 * Pour un lien de type "order", l'URL pointe vers le détail de la commande —
 * côté admin (admin.orders.show) ou côté client (commande.show) selon le rôle
 * de l'utilisateur connecté.
 */
class NotificationLink
{
    public static function url(array $data): string
    {
        // Ancien format : une URL est déjà fournie
        if (! empty($data['url'])) {
            return $data['url'];
        }

        $link = $data['link'] ?? null;
        if (is_array($link)) {
            $type = $link['type'] ?? null;
            $id = $link['id'] ?? null;

            switch ($type) {
                case 'order':
                    if ($id) {
                        return Auth::user()?->isAdmin()
                            ? route('admin.orders.show', $id)
                            : route('commande.show', $id);
                    }
                    break;
                case 'home':
                    return route('home');
                case 'wallet':
                    // Pas de page portefeuille web dédiée : on renvoie au tableau de bord
                    return route('dashboard');
                case 'admin_payment':
                case 'admin_wallet':
                    // Notification admin : page de modération des paiements
                    return route('admin.payments.moderation');
                case 'admin_order':
                    // Notification admin : détail de la commande
                    if ($id) {
                        return route('admin.orders.show', $id);
                    }
                    break;
                case 'admin_user':
                    // Notification admin : fiche utilisateur (ex : vendeur à valider)
                    if ($id) {
                        return route('admin.users.show', $id);
                    }
                    break;
            }
        }

        return '#';
    }

    /** Vrai si la notification mène réellement quelque part. */
    public static function hasUrl(array $data): bool
    {
        return self::url($data) !== '#';
    }

    /** Texte principal de la notification (tolère les deux formats). */
    public static function message(array $data): string
    {
        return $data['message'] ?? $data['body'] ?? '';
    }

    /** Titre / expéditeur affiché. */
    public static function title(array $data): string
    {
        return $data['user_name'] ?? $data['title'] ?? 'Système';
    }
}
