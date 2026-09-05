<header class="site-header">
    <div class="container">
        <div class="header-inner d-flex align-items-center justify-content-between">
            <!-- Logo de la marque -->
            <a href="{{ route('home') }}" class="brand-logo text-decoration-none d-flex align-items-center gap-2">
                <div class="logo-delta d-flex align-items-center justify-content-center" style="color: #00E5FF; filter: drop-shadow(0 0 10px rgba(0, 229, 255, 0.7)); font-size: 20px;">
                    <i class="fas fa-play" style="transform: rotate(-90deg);"></i>
                </div>
                <span class="fw-bold text-white fs-7 text-uppercase" style="letter-spacing: 2px;">AETHERA</span>
            </a>

            <!-- Navigation Principale PC Desktop (En Français) -->
            <ul class="center-nav d-none d-lg-flex align-items-center gap-4 list-unstyled mb-0">
                <li>
                    <a href="{{ route('home') }}" class="text-decoration-none {{ request()->routeIs('home') ? 'active' : '' }}">
                        Accueil
                    </a>
                </li>
                <li>
                    <a href="{{ route('products', ['category' => 'electronique']) }}" class="text-decoration-none {{ request()->is('*electronique*') ? 'active' : '' }}">
                        Audio & Son
                    </a>
                </li>
                <li>
                    <a href="{{ route('products', ['category' => 'telephones-portables']) }}" class="text-decoration-none">
                        High-Tech
                    </a>
                </li>
                <li>
                    <a href="{{ route('products', ['category' => 'mode']) }}" class="text-decoration-none">
                        Accessoires
                    </a>
                </li>
                <li>
                    <a href="{{ route('products') }}" class="text-decoration-none {{ request()->routeIs('products') && !request('category') ? 'active' : '' }}">
                        Catalogue
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="text-decoration-none {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        Catégories
                    </a>
                </li>
            </ul>

            <!-- Actions à Droite (Devise, Recherche, Profil, Panier en Français) -->
            <div class="header-right d-flex align-items-center gap-3">
                <span class="currency-label text-secondary fw-semibold fs-10 d-none d-md-inline" style="letter-spacing: 1px;">FCFA ▾</span>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="icon-btn-header text-secondary fs-8" title="Mon Compte"><i class="far fa-user"></i></a>
                @else
                    <a href="{{ route('login') }}" class="icon-btn-header text-secondary fs-8" title="Connexion"><i class="far fa-user"></i></a>
                @endauth
                
                <a href="{{ route('products') }}" class="icon-btn-header text-secondary fs-8" title="Rechercher"><i class="fas fa-search"></i></a>
                
                <!-- Bouton Pilule Panier Lumineux -->
                <a href="{{ route('panier') }}" class="cart-pill-btn text-decoration-none d-flex align-items-center gap-2" title="Voir le Panier">
                    <i class="fas fa-shopping-cart"></i>
                    @livewire('navbar-cart-count')
                </a>
            </div>
        </div>

        <!-- Barre de Recherche Mobile (En Français) -->
        <div class="mobile-search-bar d-block d-lg-none mt-3 position-relative">
            <i class="fas fa-search mobile-search-icon position-absolute" style="left: 16px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 13px;"></i>
            <input type="text" class="mobile-search-input w-100" placeholder="Rechercher appareils, audio, tech..." style="background: rgba(18, 26, 43, 0.85); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 999px; padding: 10px 18px 10px 42px; color: #FFF; font-size: 13px; outline: none;">
        </div>
    </div>
</header>

<style>
    .site-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: rgba(10, 14, 23, 0.92);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 14px 0;
    }

    .center-nav a {
        color: #94A3B8;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        transition: all 0.25s ease;
        position: relative;
    }

    .center-nav a:hover, .center-nav a.active {
        color: #00E5FF !important;
        text-shadow: 0 0 12px rgba(0, 229, 255, 0.6);
    }

    .center-nav a.active::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #00E5FF;
        box-shadow: 0 0 8px #00E5FF;
        border-radius: 2px;
    }

    .icon-btn-header {
        transition: color 0.2s ease;
    }

    .icon-btn-header:hover {
        color: #00E5FF !important;
    }

    .cart-pill-btn {
        background: rgba(0, 229, 255, 0.15);
        border: 1px solid rgba(0, 229, 255, 0.5);
        border-radius: 999px;
        padding: 6px 14px;
        color: #00E5FF;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.25);
        transition: all 0.25s ease;
    }

    .cart-pill-btn:hover {
        background: rgba(0, 229, 255, 0.25);
        box-shadow: 0 0 22px rgba(0, 229, 255, 0.5);
        transform: scale(1.03);
    }
</style>
