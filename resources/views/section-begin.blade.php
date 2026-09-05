<section class="py-2 border-bottom border-translucent position-sticky top-0 z-3 bg-body" style="backdrop-filter: blur(16px); background: rgba(8, 12, 20, 0.88) !important;">
    <div class="container-small">
        <div class="ecommerce-topbar">
            <nav class="navbar navbar-expand navbar-light px-0 py-1">
                <div class="row gx-2 gy-2 w-100 align-items-center justify-content-between">
                    <!-- Brand Logo -->
                    <div class="col-auto">
                        <a class="text-decoration-none d-flex align-items-center gap-2" href="{{ route('home') }}">
                            <div class="logo-symbol d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #38BDF8 0%, #0284C7 100%); color: #080C14; box-shadow: 0 0 12px rgba(56, 189, 248, 0.35);">
                                <i class="fas fa-cube fs-8"></i>
                            </div>
                            <span class="fw-bold fs-7 text-white text-uppercase tracking-wider">
                                NEXUS<span style="color: #38BDF8;">LUXE</span>
                            </span>
                        </a>
                    </div>

                    <!-- Search Bar Center -->
                    <div class="col-12 col-md-6 order-3 order-md-2 mt-2 mt-md-0">
                        @livewire('search-ecommerce')
                    </div>

                    <!-- Right Icons (Desktop & Mobile essentials) -->
                    <div class="col-auto order-2 order-md-3">
                        <ul class="navbar-nav navbar-nav-icons flex-row align-items-center gap-2 me-n2">
                            {{-- Theme Switcher (Desktop only) --}}
                            <li class="nav-item d-none d-md-flex align-items-center">
                                <div class="theme-control-toggle fa-icon-wait px-1">
                                    <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                                        data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                                    <label class="mb-0 theme-control-toggle-label theme-control-toggle-light"
                                        for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="Changer de thème" style="height:32px;width:32px;">
                                        <span class="icon" data-feather="moon"></span>
                                    </label>
                                    <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark"
                                        for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="Changer de thème" style="height:32px;width:32px;">
                                        <span class="icon" data-feather="sun"></span>
                                    </label>
                                </div>
                            </li>

                            {{-- Wishlist Icon (Desktop only) --}}
                            <li class="nav-item d-none d-md-block">
                                <a class="nav-link px-2 text-body-tertiary" href="{{ route('favoris') }}" title="Liste de souhaits">
                                    <i class="far fa-heart fs-7"></i>
                                </a>
                            </li>

                            {{-- Cart with Count Badge --}}
                            <li class="nav-item">
                                @livewire('navbar-cart-count')
                            </li>

                            {{-- User Profile / Login (Desktop only - on mobile accessible via bottom navbar) --}}
                            <li class="nav-item dropdown d-none d-md-block">
                                @if (isset($user) || auth()->check())
                                    @php $currentUser = $user ?? auth()->user(); @endphp
                                    <a class="nav-link px-2" id="navbarDropdownUser" href="#" role="button"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                                        aria-expanded="false">
                                        <span class="text-body-tertiary" data-feather="user" style="height:20px;width:20px;"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border mt-2"
                                        aria-labelledby="navbarDropdownUser">
                                        <div class="card position-relative border-0" style="background: #0E1524 !important;">
                                            <div class="card-body p-0">
                                                <div class="text-center pt-4 pb-3">
                                                    <div class="avatar avatar-xl">
                                                        <img class="rounded-circle"
                                                            src="{{ $currentUser?->getPhoto()?->getImageUrl(120, 120) ?? asset('assets/img/team/15.webp') }}"
                                                            alt="" />
                                                    </div>
                                                    <h6 class="mt-2 text-white">{{ $currentUser->name }}</h6>
                                                    <a href="{{ route('dashboard') }}" class="badge bg-primary-subtle text-primary text-decoration-none mt-1">Tableau de bord</a>
                                                </div>
                                            </div>
                                            <div class="p-3 border-top border-translucent">
                                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center w-100">
                                                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a class="nav-link px-2 text-body-tertiary" href="{{ route('login') }}" role="button" title="Se connecter">
                                        <i class="far fa-user fs-7"></i>
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</section>
