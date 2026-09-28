<?php

namespace App\Support;

use App\Models\Produits;

class WhatsApp
{
    /**
     * Numéro WhatsApp normalisé (chiffres, avec indicatif).
     * Configurable via WHATSAPP_NUMBER dans .env.
     * Ex: "64356060" (8 chiffres Mali) => "22364356060".
     */
    public static function number(): string
    {
        $raw = (string) config('app.whatsapp_number', '');
        $digits = preg_replace('/\D+/', '', $raw);

        if ($digits === '') {
            return '22382019583';
        }

        // Déjà au format international (ex: 22382019583)
        if (str_starts_with($digits, '223') && strlen($digits) >= 11) {
            return $digits;
        }

        // Numéro local malien à 8 chiffres => préfixe 223
        if (strlen($digits) === 8) {
            return '223' . $digits;
        }

        // 00223... ou +223... déjà nettoyé
        if (str_starts_with($digits, '00223')) {
            return substr($digits, 2);
        }

        return $digits;
    }

    public static function link(string $message): string
    {
        return 'https://wa.me/' . static::number() . '?text=' . rawurlencode($message);
    }

    public static function formatFcfa($amount): string
    {
        return number_format((float) $amount, 0, ',', ' ') . ' FCFA';
    }

    public static function effectivePrice(Produits $product): float
    {
        if ($product->sale_price && (float) $product->sale_price < (float) $product->price) {
            return (float) $product->sale_price;
        }

        return (float) $product->price;
    }

    /**
     * Message standard pour une carte produit (sans options choisies).
     */
    public static function productMessage(Produits $product, ?string $url = null): string
    {
        $price = static::formatFcfa(static::effectivePrice($product));
        $msg = "Bonjour, je veux acheter « {$product->name} » à {$price} (Réf: " . ($product->sku ?: 'N°' . $product->id) . ')';
        if ($url) {
            $msg .= ' : ' . $url;
        }

        return $msg;
    }

    public static function productUrl(Produits $product, ?string $url = null): string
    {
        return static::link(static::productMessage($product, $url));
    }

    /**
     * Message avec options sélectionnées (fiche produit / panier).
     * $options = ['Stockage' => '256 Go', 'Couleur' => 'Noir', ...]
     */
    public static function orderMessage(Produits $product, array $options = [], int $qty = 1, ?string $url = null): string
    {
        $price = static::formatFcfa(static::effectivePrice($product));
        $msg = "Bonjour, je veux commander {$qty}x « {$product->name} » à {$price}";
        if (!empty($options)) {
            $parts = [];
            foreach ($options as $k => $v) {
                $parts[] = "{$k} : {$v}";
            }
            $msg .= ' (' . implode(', ', $parts) . ')';
        }
        if ($url) {
            $msg .= ' : ' . $url;
        }

        return $msg;
    }

    public static function orderUrl(Produits $product, array $options = [], int $qty = 1, ?string $url = null): string
    {
        return static::link(static::orderMessage($product, $options, $qty, $url));
    }
}
