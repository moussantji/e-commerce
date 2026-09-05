{{-- ==========================================================
     NAVBAR MOBILE EN BAS ULTRA-PREMIUM (PROPOSITION 3 NEO-LUXE)
     Pas de boutons empilés, navigation fluide au pouce
     ========================================================== --}}
<nav class="mobile-bottom-dock d-flex d-lg-none" aria-label="Navigation Mobile Inférieure">
    <!-- Accueil -->
    <a href="{{ route('home') }}" class="dock-tab {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-house"></i>
        <span>Accueil</span>
    </a>

    <!-- Catalogue / Produits -->
    <a href="{{ route('products') }}" class="dock-tab {{ request()->routeIs('products') || request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i>
        <span>Produits</span>
    </a>

    <!-- Panier avec badge dynamique -->
    <a href="{{ route('panier') }}" class="dock-tab {{ request()->routeIs('panier') ? 'active' : '' }}">
        <i class="fas fa-bag-shopping"></i>
        @livewire('navbar-cart-count')
        <span>Panier</span>
    </a>

    <!-- Favoris / Wishlist -->
    <a href="{{ route('favoris') }}" class="dock-tab {{ request()->routeIs('favoris') ? 'active' : '' }}">
        <i class="fas fa-heart"></i>
        <span>Favoris</span>
    </a>

    <!-- Compte / Profil -->
    @auth
        <a href="{{ route('dashboard') }}" class="dock-tab {{ request()->routeIs('dashboard') || request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
            <span>Compte</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="dock-tab {{ request()->routeIs('login') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Connexion</span>
        </a>
    @endauth
</nav>

<style>
/* STYLES NAVBAR MOBILE ULTRA PREMIUM */
.mobile-bottom-dock {
    position: fixed;
    bottom: 12px;
    left: 14px;
    right: 14px;
    height: 64px;
    background: rgba(14, 21, 36, 0.92);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 32px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.7), 0 0 20px rgba(56, 189, 248, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-around;
    padding: 0 8px;
    z-index: 1040;
}

@media (min-width: 992px) {
    .mobile-bottom-dock {
        display: none !important;
    }
}

.mobile-bottom-dock .dock-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #64748B;
    font-size: 10.5px;
    font-weight: 600;
    width: 54px;
    height: 48px;
    border-radius: 18px;
    position: relative;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-bottom-dock .dock-tab i {
    font-size: 18px;
    margin-bottom: 2px;
    transition: transform 0.2s ease;
}

.mobile-bottom-dock .dock-tab.active {
    color: #38BDF8 !important;
    background: rgba(56, 189, 248, 0.12);
}

.mobile-bottom-dock .dock-tab.active i {
    transform: scale(1.15);
    filter: drop-shadow(0 0 8px rgba(56, 189, 248, 0.6));
}

.mobile-bottom-dock .dock-tab:hover:not(.active) {
    color: #F8FAFC;
}

/* Ajustement du padding bas du body sur mobile */
@media (max-width: 991.98px) {
    body {
        padding-bottom: 85px !important;
    }
}
</style>
