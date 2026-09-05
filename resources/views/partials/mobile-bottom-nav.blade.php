{{-- ==========================================================
     EXACT MOBILE BOTTOM DOCK (FROM PROP3_MOBILE.PNG)
     ========================================================== --}}
<nav class="mobile-bottom-dock d-flex d-lg-none" aria-label="Mobile Bottom Navigation">
    <!-- HOME -->
    <a href="{{ route('home') }}" class="dock-tab {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Home</span>
        @if(request()->routeIs('home'))
            <div class="dock-active-dot"></div>
        @endif
    </a>

    <!-- CATALOG -->
    <a href="{{ route('products') }}" class="dock-tab {{ request()->routeIs('products') || request()->routeIs('categories.*') ? 'active' : '' }}">
        <i class="fas fa-border-all"></i>
        <span>Catalog</span>
        @if(request()->routeIs('products') || request()->routeIs('categories.*'))
            <div class="dock-active-dot"></div>
        @endif
    </a>

    <!-- CART WITH BADGE -->
    <a href="{{ route('panier') }}" class="dock-tab {{ request()->routeIs('panier') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i>
        @livewire('navbar-cart-count')
        <span>Cart</span>
    </a>

    <!-- FAVORITES -->
    <a href="{{ route('favoris') }}" class="dock-tab {{ request()->routeIs('favoris') ? 'active' : '' }}">
        <i class="far fa-star"></i>
        <span>Favorites</span>
    </a>

    <!-- PROFILE -->
    @auth
        <a href="{{ route('dashboard') }}" class="dock-tab {{ request()->routeIs('dashboard') || request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="far fa-user"></i>
            <span>Profile</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="dock-tab {{ request()->routeIs('login') ? 'active' : '' }}">
            <i class="far fa-user"></i>
            <span>Profile</span>
        </a>
    @endauth
</nav>

<style>
.mobile-bottom-dock {
    position: fixed;
    bottom: 14px;
    left: 16px;
    right: 16px;
    height: 64px;
    background: rgba(14, 20, 32, 0.94);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(0, 229, 255, 0.35);
    border-radius: 32px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.85), 0 0 20px rgba(0, 229, 255, 0.2);
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
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    width: 54px;
    height: 50px;
    border-radius: 18px;
    position: relative;
    transition: all 0.25s ease;
}

.mobile-bottom-dock .dock-tab i {
    font-size: 17px;
    margin-bottom: 3px;
    transition: transform 0.2s ease;
}

.mobile-bottom-dock .dock-tab.active {
    color: #00E5FF !important;
}

.mobile-bottom-dock .dock-tab.active i {
    filter: drop-shadow(0 0 10px #00E5FF);
}

.mobile-bottom-dock .dock-active-dot {
    position: absolute;
    bottom: 4px;
    width: 4px;
    height: 4px;
    background: #00F5A0;
    border-radius: 50%;
    box-shadow: 0 0 8px #00F5A0;
}
</style>
