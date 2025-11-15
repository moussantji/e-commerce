<nav class="navbar navbar-vertical navbar-expand-lg">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">

                <!-- Tableau de bord -->
                <li class="nav-item">
                    <p class="navbar-vertical-label">TABLEAU DE BORD</p>
                    <hr class="navbar-vertical-line" />
                    <div class="nav-item-wrapper">
                        <a class="nav-link label-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" role="button">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon">
                                    <span data-feather="pie-chart"></span>
                                </span>
                                <span class="nav-link-text-wrapper">
                                    <span class="nav-link-text">Tableau de bord</span>
                                </span>
                            </div>
                        </a>
                    </div>
                </li>

                <!-- Gestion de la boutique -->
                <p class="navbar-vertical-label">GESTION DE LA BOUTIQUE</p>
                <hr class="navbar-vertical-line" />

                <!-- Section Produits -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                            href="#nv-products" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-products">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="package"></span>
                                </span>
                                <span class="nav-link-text">Produits</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-products">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}" 
                                        href="{{ route('admin.products.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Liste des produits</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}" 
                                        href="{{ route('admin.products.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Ajouter un produit</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Section Catégories -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                            href="#nv-categories" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.categories.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-categories">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="grid"></span>
                                </span>
                                <span class="nav-link-text">Catégories</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-categories">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}" 
                                        href="{{ route('admin.categories.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Liste des catégories</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}" 
                                        href="{{ route('admin.categories.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Ajouter une catégorie</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Section Tags -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" 
                            href="#nv-tags" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.tags.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-tags">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="tag"></span>
                                </span>
                                <span class="nav-link-text">Tags</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-tags">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.tags.index') ? 'active' : '' }}" 
                                        href="{{ route('admin.tags.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Liste des tags</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.tags.create') ? 'active' : '' }}" 
                                        href="{{ route('admin.tags.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Ajouter un tag</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Gestion des commandes -->
                <p class="navbar-vertical-label">GESTION DES COMMANDES</p>
                <hr class="navbar-vertical-line" />

                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" 
                            href="#nv-orders" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-orders">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="shopping-bag"></span>
                                </span>
                                <span class="nav-link-text">Commandes</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-orders">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.orders.index') && !request()->has('status') ? 'active' : '' }}" 
                                        href="{{ route('admin.orders.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Toutes les commandes</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('status') === 'en_attente' ? 'active' : '' }}" 
                                        href="{{ route('admin.orders.index') }}?status=en_attente">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Commandes en attente</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('status') === 'en_cours' ? 'active' : '' }}" 
                                        href="{{ route('admin.orders.index') }}?status=en_cours">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Commandes en cours</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('status') === 'expediee' ? 'active' : '' }}" 
                                        href="{{ route('admin.orders.index') }}?status=expediee">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Commandes terminées</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Gestion des livraisons -->
                <p class="navbar-vertical-label">GESTION DES LIVRAISONS</p>
                <hr class="navbar-vertical-line" />

                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.shipping-methods.*') ? 'active' : '' }}" 
                            href="#nv-shipping" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.shipping-methods.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-shipping">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="truck"></span>
                                </span>
                                <span class="nav-link-text">Méthodes de livraison</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-shipping">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.shipping-methods.index') ? 'active' : '' }}" 
                                        href="{{ route('admin.shipping-methods.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Liste des méthodes</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.shipping-methods.create') ? 'active' : '' }}" 
                                        href="{{ route('admin.shipping-methods.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Ajouter une méthode</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Gestion des paiements -->
                <p class="navbar-vertical-label">GESTION DES PAIEMENTS</p>
                <hr class="navbar-vertical-line" />

                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}" 
                            href="#nv-payment" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.payment-methods.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-payment">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="credit-card"></span>
                                </span>
                                <span class="nav-link-text">Méthodes de paiement</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-payment">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.payment-methods.index') ? 'active' : '' }}" 
                                        href="{{ route('admin.payment-methods.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Liste des méthodes</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.payment-methods.create') ? 'active' : '' }}" 
                                        href="{{ route('admin.payment-methods.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Ajouter une méthode</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Paramètres de la boutique -->
                <p class="navbar-vertical-label">PARAMÈTRES DE LA BOUTIQUE</p>
                <hr class="navbar-vertical-line" />

                <!-- Section Coupons -->
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" 
                            href="#nv-coupons" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.coupons.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-coupons">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="tag"></span>
                                </span>
                                <span class="nav-link-text">Coupons</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-coupons">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.coupons.index') ? 'active' : '' }}" 
                                        href="{{ route('admin.coupons.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Liste des coupons</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.coupons.create') ? 'active' : '' }}" 
                                        href="{{ route('admin.coupons.create') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Créer un coupon</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                            href="#nv-users" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-users">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="users"></span>
                                </span>
                                <span class="nav-link-text">Utilisateurs</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-users">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users.index') && !request()->has('role') ? 'active' : '' }}" 
                                        href="{{ route('admin.users.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Tous les utilisateurs</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('role') === 'customer' ? 'active' : '' }}" 
                                        href="{{ route('admin.users.index') }}?role=customer">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Clients</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->get('role') === 'admin' ? 'active' : '' }}" 
                                        href="{{ route('admin.users.index') }}?role=admin">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-text">Administrateurs</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <!-- Paramètres -->
                <p class="navbar-vertical-label">PARAMÈTRES</p>
                <hr class="navbar-vertical-line" />
                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                            href="#nv-settings" role="button" data-bs-toggle="collapse" 
                            aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}" 
                            aria-controls="nv-settings">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon"></span>
                                </div>
                                <span class="nav-link-icon">
                                    <span data-feather="settings"></span>
                                </span>
                                <span class="nav-link-text">Paramètres</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse"
                                id="nv-settings">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="user"></span></span>
                                            <span class="nav-link-text">Mon profil</span>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="log-out"></span></span>
                                            <span class="nav-link-text">Déconnexion</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer"><button
            class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span
                class="uil uil-left-arrow-to-left fs-8"></span><span
                class="uil uil-arrow-from-right fs-8"></span><span class="navbar-vertical-footer-text ms-2">Collapsed
                View</span></button></div>
</nav>
