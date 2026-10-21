<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Livraison offerte
    |--------------------------------------------------------------------------
    |
    | La livraison est offerte uniquement lorsque le panier contient PLUS DE
    | 2 articles (donc 3 minimum) ET que le sous-total atteint au moins
    | 100 000 FCFA.
    |
    */
    'free_shipping_min_items' => (int) env('FREE_SHIPPING_MIN_ITEMS', 3),
    'free_shipping_min_total' => (float) env('FREE_SHIPPING_MIN_TOTAL', 100000),

    /*
    |--------------------------------------------------------------------------
    | Distribution automatique des coupons
    |--------------------------------------------------------------------------
    |
    | Une commande déclenche l'application directe du coupon à partir de
    | 10 articles, ou dès que son sous-total dépasse 100 000 FCFA.
    | Un coupon est attribué au client lorsque ses commandes payées du mois
    | dépassent 20 articles, ou atteignent 200 000 FCFA de valeur totale.
    |
    */
    'coupon_order_min_quantity' => (int) env('COUPON_ORDER_MIN_QUANTITY', 10),
    'coupon_order_min_subtotal' => (float) env('COUPON_ORDER_MIN_SUBTOTAL', 100000),
    'coupon_month_min_quantity' => (int) env('COUPON_MONTH_MIN_QUANTITY', 20),
    'coupon_month_min_total' => (float) env('COUPON_MONTH_MIN_TOTAL', 200000),

    'currency' => 'FCFA',
];
