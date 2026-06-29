@php
    // Compteur du panier (chargé au rendu de la page)
    $bottomCartCount = auth()->check()
        ? (int) \Illuminate\Support\Facades\DB::table('panier_produit')
            ->join('paniers', 'panier_produit.paniers_id', '=', 'paniers.id')
            ->where('paniers.user_id', auth()->id())
            ->sum('panier_produit.quantite')
        : (int) session('cart_count', 0);
@endphp

<nav class="mobile-bottom-nav d-lg-none" aria-label="Navigation mobile">
    <a href="{{ route('home') }}"
       class="mobile-bottom-nav__item {{ request()->routeIs('home') ? 'active' : '' }}">
        <span class="fas fa-home mobile-bottom-nav__icon"></span>
        <span class="mobile-bottom-nav__label">Accueil</span>
    </a>

    <a href="{{ route('categories.index') }}"
       class="mobile-bottom-nav__item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
        <span class="fas fa-th-large mobile-bottom-nav__icon"></span>
        <span class="mobile-bottom-nav__label">Catégories</span>
    </a>

    <a href="{{ route('products') }}"
       class="mobile-bottom-nav__item {{ request()->routeIs('products') ? 'active' : '' }}">
        <span class="fas fa-shopping-bag mobile-bottom-nav__icon"></span>
        <span class="mobile-bottom-nav__label">Produits</span>
    </a>

    <a href="{{ auth()->check() ? route('favoris') : route('login') }}"
       class="mobile-bottom-nav__item {{ request()->routeIs('favoris') ? 'active' : '' }}">
        <span class="fas fa-heart mobile-bottom-nav__icon"></span>
        <span class="mobile-bottom-nav__label">Favoris</span>
    </a>

    <a href="{{ route('panier') }}"
       class="mobile-bottom-nav__item {{ request()->routeIs('panier') ? 'active' : '' }}">
        <span class="mobile-bottom-nav__icon position-relative">
            <span class="fas fa-shopping-cart"></span>
            <span class="mobile-bottom-nav__badge"
                  data-cart-badge
                  @if($bottomCartCount === 0) hidden @endif>{{ $bottomCartCount }}</span>
        </span>
        <span class="mobile-bottom-nav__label">Panier</span>
    </a>
</nav>

<style>
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        display: flex;
        justify-content: space-around;
        align-items: stretch;
        background: var(--phoenix-body-emphasis-bg, #fff);
        border-top: 1px solid var(--phoenix-border-color-translucent, rgba(0, 0, 0, 0.1));
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
        padding-bottom: env(safe-area-inset-bottom, 0px);
        height: calc(60px + env(safe-area-inset-bottom, 0px));
    }

    .mobile-bottom-nav__item {
        flex: 1 1 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        text-decoration: none;
        color: var(--phoenix-body-tertiary-color, #6c757d);
        font-size: 0.68rem;
        font-weight: 600;
        padding: 6px 0;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .mobile-bottom-nav__item:active {
        transform: scale(0.92);
    }

    .mobile-bottom-nav__icon {
        font-size: 1.15rem;
        line-height: 1;
    }

    .mobile-bottom-nav__item.active {
        color: var(--phoenix-primary, #3874ff);
    }

    .mobile-bottom-nav__item.active .mobile-bottom-nav__icon {
        transform: translateY(-1px);
    }

    .mobile-bottom-nav__label {
        line-height: 1;
    }

    .mobile-bottom-nav__badge {
        position: absolute;
        top: -7px;
        right: -10px;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        border-radius: 999px;
        background: var(--phoenix-danger, #e63757);
        color: #fff;
        font-size: 0.6rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    /* Évite que le contenu / footer soit masqué par la barre */
    @media (max-width: 991.98px) {
        body {
            padding-bottom: calc(60px + env(safe-area-inset-bottom, 0px));
        }
    }
</style>

<script>
    // Met à jour le badge du panier quand Livewire signale un changement
    document.addEventListener('livewire:init', () => {
        Livewire.on('refresh-navbar-cart', () => {
            // Laisse Livewire mettre à jour le compteur de la navbar, puis on synchronise
            const navbarBadge = document.querySelector('.icon-indicator-number');
            const bottomBadge = document.querySelector('[data-cart-badge]');
            if (navbarBadge && bottomBadge) {
                setTimeout(() => {
                    const count = parseInt(navbarBadge.textContent.trim()) || 0;
                    bottomBadge.textContent = count;
                    bottomBadge.hidden = count === 0;
                }, 300);
            }
        });
    });
</script>
