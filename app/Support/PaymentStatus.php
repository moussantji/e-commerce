<?php

namespace App\Support;

/**
 * Source unique de vérité pour les statuts de paiement.
 *
 * Utilisée par le site web (Blade), les contrôleurs et exposée à l'app mobile
 * via l'API afin que le site et l'application affichent EXACTEMENT les mêmes
 * libellés et couleurs.
 *
 * Vocabulaire canonique (codes stockés en base) :
 *   - pending   : en attente de vérification
 *   - confirmed : validé par l'admin
 *   - rejected  : rejeté par l'admin
 *   - cod       : paiement à la livraison (espèces)
 */
class PaymentStatus
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const REJECTED = 'rejected';
    public const COD = 'cod';

    /** Libellés français, partagés entre web et mobile. */
    public const LABELS = [
        self::PENDING => 'En attente',
        self::CONFIRMED => 'Confirmé',
        self::REJECTED => 'Rejeté',
        self::COD => 'À la livraison',
    ];

    /** Classe de couleur Bootstrap (site web). */
    public const BADGE_CLASSES = [
        self::PENDING => 'warning',
        self::CONFIRMED => 'success',
        self::REJECTED => 'danger',
        self::COD => 'info',
    ];

    /** Couleur hexadécimale (app mobile / usage générique). */
    public const COLORS = [
        self::PENDING => '#f59e0b',
        self::CONFIRMED => '#16a34a',
        self::REJECTED => '#dc2626',
        self::COD => '#2563eb',
    ];

    /**
     * Ramène d'anciennes valeurs vers le vocabulaire canonique.
     * (les preuves de paiement utilisaient auparavant 'confirme' / 'rejete')
     */
    public static function normalize(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'confirme', 'confirmé', 'paye', 'payee', 'payé', 'paid', 'valide', 'validé', 'paiement_accepte', 'accepte' => self::CONFIRMED,
            'rejete', 'rejeté', 'refuse', 'refusé', 'annule', 'annulé' => self::REJECTED,
            'en_attente', 'attente', '' => self::PENDING,
            default => $status,
        };
    }

    /** Libellé français d'un statut (tolère les anciennes valeurs). */
    public static function label(?string $status): string
    {
        $code = self::normalize($status);

        return self::LABELS[$code] ?? ucfirst(str_replace('_', ' ', $code));
    }

    /** Classe de couleur Bootstrap d'un statut. */
    public static function badgeClass(?string $status): string
    {
        $code = self::normalize($status);

        return self::BADGE_CLASSES[$code] ?? 'secondary';
    }

    /** Couleur hexadécimale d'un statut. */
    public static function color(?string $status): string
    {
        $code = self::normalize($status);

        return self::COLORS[$code] ?? '#6b7280';
    }
}
