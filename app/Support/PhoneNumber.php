<?php

namespace App\Support;

/**
 * Numéros maliens : 8 chiffres (fixes/opérateurs 6x, 7x, 9x...).
 * Normalise vers le format international 223XXXXXXXX.
 */
class PhoneNumber
{
    /**
     * Normalise un numéro vers 223XXXXXXXX, ou null si invalide.
     */
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        // 00223XXXXXXXX -> 223XXXXXXXX
        if (str_starts_with($digits, '00223')) {
            $digits = substr($digits, 2);
        }

        // XXXXXXXX local -> 223XXXXXXXX
        if (strlen($digits) === 8) {
            $digits = '223' . $digits;
        }

        if (!preg_match('/^223[679]\d{7}$/', $digits)) {
            return null;
        }

        return $digits;
    }

    public static function valid(?string $phone): bool
    {
        return $phone !== null && $phone !== '' && static::normalize($phone) !== null;
    }

    /** Affichage local : 70 00 00 00. */
    public static function pretty(?string $phone): string
    {
        $n = static::normalize($phone);
        if (!$n) {
            return (string) $phone;
        }

        return substr($n, 3, 2) . ' ' . substr($n, 5, 2) . ' ' . substr($n, 7, 2) . ' ' . substr($n, 9, 2);
    }
}
