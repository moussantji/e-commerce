{{-- Navigation admin style boutique (comme la nav de l'accueil). --}}
@php
    $adminLinks = [
        ['admin.dashboard', 'admin.dashboard', 'Tableau de bord'],
        ['admin.orders.index', 'admin.orders.*', 'Commandes'],
        ['admin.products.index', 'admin.products.*', 'Produits'],
        ['admin.categories.index', 'admin.categories.*', 'Catégories'],
        ['admin.brands.index', 'admin.brands.*', 'Marques'],
        ['admin.caracteristiques.index', 'admin.caracteristiques.*', 'Caractéristiques'],
        ['admin.tags.index', 'admin.tags.*', 'Tags'],
        ['admin.coupons.index', 'admin.coupons.*', 'Coupons'],
        ['admin.banners.index', 'admin.banners.*', 'Bannières'],
        ['admin.users.index', 'admin.users.*', 'Clients'],
        ['admin.payments.moderation', 'admin.payments.*', 'Paiements'],
        ['admin.payment-methods.index', 'admin.payment-methods.*', 'Moyens paiement'],
        ['admin.shipping-methods.index', 'admin.shipping-methods.*', 'Livraison'],
    ];
@endphp
<nav class="admin-nav" aria-label="Administration">
    <div class="shop-wrap nav-row">
        @foreach ($adminLinks as [$route, $pattern, $label])
            @if (Route::has($route))
                <a href="{{ route($route) }}"
                    class="{{ request()->routeIs($pattern) ? 'hot' : '' }}">{{ $label }}</a>
            @endif
        @endforeach
    </div>
</nav>
