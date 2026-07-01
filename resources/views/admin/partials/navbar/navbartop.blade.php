<nav class="navbar navbar-top fixed-top navbar-expand-lg justify-content-center" id="navbarTop" style="display:none;">
    <div class="navbar-logo">
        <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse"
            aria-expanded="false" aria-label="Toggle Navigation">
            <span class="navbar-toggle-icon">
                <span class="toggle-line"></span>
            </span>
        </button>
        <a class="navbar-brand me-1 me-sm-3" href="{{ route('admin.dashboard') }}">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('assets/img/icons/logo.png') }}" alt="Logo" width="27" />
                    <h5 class="logo-text ms-2 d-none d-sm-block">Admin</h5>
                </div>
            </div>
        </a>
    </div>
    <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center"
        id="navbarTopCollapse">
        <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
            <!-- Tableau de bord -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span class="uil fs-8 me-2" data-feather="pie-chart"></span>
                    Tableau de bord
                </a>
            </li>

            <!-- Gestion de la boutique -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                    href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-haspopup="true"
                    aria-expanded="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }}">
                    <span class="uil fs-8 me-2" data-feather="shopping-bag"></span>
                    Boutique
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                            href="{{ route('admin.products.index') }}">
                            <span class="me-2" data-feather="package"></span>Produits
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                            href="{{ route('admin.categories.index') }}">
                            <span class="me-2" data-feather="grid"></span>Catégories
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Commandes -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                    href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.orders.*') ? 'true' : 'false' }}">
                    <span class="uil fs-8 me-2" data-feather="shopping-cart"></span>
                    Commandes
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('admin.orders.index') && !request()->has('status') ? 'active' : '' }}"
                            href="{{ route('admin.orders.index') }}">
                            Toutes les commandes
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->get('status') === 'en_attente' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index') }}?status=en_attente">
                            En attente
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->get('status') === 'traitement' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index') }}?status=traitement">
                            En préparation
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->get('status') === 'livre' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index') }}?status=livre">
                            Livrées
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Utilisateurs -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-haspopup="true" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}">
                    <span class="uil fs-8 me-2" data-feather="users"></span>
                    Utilisateurs
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('admin.users.index') && !request()->has('role') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            Tous les utilisateurs
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->get('role') === 'customer' ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}?role=customer">
                            Clients
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->get('role') === 'admin' ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}?role=admin">
                            Administrateurs
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Paramètres -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.profile') ? 'active' : '' }}"
                    href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-haspopup="true"
                    aria-expanded="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.profile') ? 'true' : 'false' }}">
                    <span class="uil fs-8 me-2" data-feather="settings"></span>
                    Paramètres
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}"
                            href="{{ route('admin.profile') }}">
                            <span class="me-2" data-feather="user"></span>Mon profil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="w-100">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <span class="me-2" data-feather="log-out"></span>Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Barre de droite avec icônes -->
    <ul class="navbar-nav navbar-nav-icons flex-row align-items-center">
        <!-- Thème sombre/clair -->
        <li class="nav-item">
            <div class="theme-control-toggle fa-icon-wait px-2">
                <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                    data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle"
                    data-bs-toggle="tooltip" data-bs-placement="left" title="Basculer en mode sombre">
                    <span class="icon" data-feather="moon"></span>
                </label>
                <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle"
                    data-bs-toggle="tooltip" data-bs-placement="left" title="Basculer en mode clair">
                    <span class="icon" data-feather="sun"></span>
                </label>
            </div>
        </li>

        <!-- Recherche -->
        <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal">
                <span data-feather="search" style="height:20px;width:20px;"></span>
            </a>
        </li>

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside">
                <span class="d-block position-relative">
                    <span data-feather="bell" style="height:20px;width:20px;"></span>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                        3
                        <span class="visually-hidden">notifications non lues</span>
                    </span>
                </span>
            </a>
            <div
                class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret">
                <div class="card position-relative border-0">
                    <div class="card-header p-2">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-body-emphasis mb-0">Notifications</h5>
                            <button class="btn btn-link p-0 fs-9 fw-normal" type="button">Tout marquer comme
                                lu</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="scrollbar-overlay" style="max-height: 27rem;">
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-m status-online me-3">
                                        <img class="rounded-circle"
                                            src="{{ asset('assets/img/team/40x40/avatar.webp') }}" alt="" />
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-0">Nouvelle commande reçue</h6>
                                        <p class="mb-0 text-body-secondary fs-9">Il y a 5 minutes</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Plus de notifications ici -->
                        </div>
                    </div>
                    <div class="card-footer p-0 border-top-0">
                        <a class="dropdown-item border-top border-2 border-transparent border-200 text-center fw-bold p-2"
                            href="{{ route('admin.orders.index') }}">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <!-- Profil utilisateur -->

        <li class="nav-item dropdown">
            <a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button"
                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-l">
                    <img class="rounded-circle" src="{{ asset('assets/img/team/40x40/avatar.webp') }}"
                        alt="Admin" />
                </div>
            </a>
            @include('admin.partials.profil.dropdown')
        </li>
    </ul>
</nav>
