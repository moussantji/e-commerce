<?php

namespace App\Support;

/**
 * Source unique de vérité pour les statuts de commande.
 *
 * Utilisée par le site web (Blade), les contrôleurs, et alignée sur l'app
 * mobile + l'API afin que tout affiche EXACTEMENT les mêmes libellés/couleurs
 * et que les filtres de la navigation (?status=...) fonctionnent réellement.
 *
 * Vocabulaire canonique (codes stockés dans commandes.statut) :
 *   - en_attente        : en attente de paiement
 *   - paiement_declare  : preuve envoyée, à vérifier par l'admin
 *   - payee             : paiement confirmé (prête à expédier)
 *   - expedie           : expédiée
 *   - livre             : livrée (terminal)
 *   - annule            : annulée (terminal)
 */
class OrderStatus
{
    public const EN_ATTENTE = 'en_attente';
    public const PAIEMENT_DECLARE = 'paiement_declare';
    public const PAYEE = 'payee';
    public const EXPEDIE = 'expedie';
    public const LIVRE = 'livre';
    public const ANNULE = 'annule';

    /** Libellés français, partagés entre web et mobile. */
    public const LABELS = [
        self::EN_ATTENTE => 'En attente de paiement',
        self::PAIEMENT_DECLARE => 'Paiement à vérifier',
        self::PAYEE => 'Payée',
        self::EXPEDIE => 'Expédiée',
        self::LIVRE => 'Livrée',
        self::ANNULE => 'Annulée',
    ];

    /** Classe de couleur Bootstrap / badge Phoenix (site web). */
    public const BADGE_CLASSES = [
        self::EN_ATTENTE => 'warning',
        self::PAIEMENT_DECLARE => 'warning',
        self::PAYEE => 'primary',
        self::EXPEDIE => 'primary',
        self::LIVRE => 'success',
        self::ANNULE => 'danger',
    ];

    /** Icône Feather (utilisée dans les tableaux admin). */
    public const ICONS = [
        self::EN_ATTENTE => 'clock',
        self::PAIEMENT_DECLARE => 'clock',
        self::PAYEE => 'credit-card',
        self::EXPEDIE => 'truck',
        self::LIVRE => 'check',
        self::ANNULE => 'x',
    ];

    /** Couleur hexadécimale (app mobile / usage générique). */
    public const COLORS = [
        self::EN_ATTENTE => '#f59e0b',
        self::PAIEMENT_DECLARE => '#f59e0b',
        self::PAYEE => '#6d28d9',
        self::EXPEDIE => '#2563eb',
        self::LIVRE => '#16a34a',
        self::ANNULE => '#dc2626',
    ];

    /** Statuts terminaux (aucune action de workflow supplémentaire). */
    public const TERMINAL = [self::LIVRE, self::ANNULE];

    /**
     * Ramène d'anciennes valeurs vers le vocabulaire canonique.
     * (le web utilisait auparavant en_cours, expediee, livree, annulee, etc.)
     */
    public static function normalize(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'en_traitement', 'en_cours', 'en preparation', 'en_preparation', 'traitement' => self::PAYEE,
            'expediee', 'expedition', 'expédiée', 'expédition' => self::EXPEDIE,
            'livree', 'livrée', 'delivered' => self::LIVRE,
            'annulee', 'annulée', 'cancelled', 'canceled' => self::ANNULE,
            'paye', 'payé', 'payé', 'paid', 'paiement_accepte' => self::PAYEE,
            '' => self::EN_ATTENTE,
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
        return self::BADGE_CLASSES[self::normalize($status)] ?? 'secondary';
    }

    /** Icône Feather d'un statut. */
    public static function icon(?string $status): string
    {
        return self::ICONS[self::normalize($status)] ?? 'alert-circle';
    }

    /** Couleur hexadécimale d'un statut. */
    public static function color(?string $status): string
    {
        return self::COLORS[self::normalize($status)] ?? '#6b7280';
    }

    /** Vrai si le statut est terminal (livré/annulé). */
    public static function isTerminal(?string $status): bool
    {
        return in_array(self::normalize($status), self::TERMINAL, true);
    }
}
