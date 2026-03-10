<section class="py-0">
    <div class="container-small">
        <div class="ecommerce-topbar">
            <nav class="navbar navbar-expand-lg navbar-light px-0">
                <div class="row gx-0 gy-2 w-100 flex-between-center">
                    <div class="col-auto"><a class="text-decoration-none" href="{{ route('home') }}">
                            <div class="d-flex align-items-center"><img src="{{ asset('assets/img/icons/logo.png') }}"
                                    alt="phoenix" width="27" />
                                <h5 class="logo-text ms-2">phoenix</h5>
                            </div>
                        </a></div>
                    <div class="col-auto order-md-1">
                        <ul class="navbar-nav navbar-nav-icons flex-row me-n2">
                            {{-- light --}}
                            <li class="nav-item d-flex align-items-center">
                                <div class="theme-control-toggle fa-icon-wait px-2"><input
                                        class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                                        data-theme-control="phoenixTheme" value="dark"
                                        id="themeControlToggle" /><label
                                        class="mb-0 theme-control-toggle-label theme-control-toggle-light"
                                        for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="Switch theme" style="height:32px;width:32px;"><span
                                            class="icon" data-feather="moon"></span></label><label
                                        class="mb-0 theme-control-toggle-label theme-control-toggle-dark"
                                        for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="Switch theme" style="height:32px;width:32px;"><span
                                            class="icon" data-feather="sun"></span></label></div>
                            </li>
                            {{-- cart --}}
                            <!-- ✅ NOUVEAU -->
                            @livewire('navbar-cart-count')
                            {{-- NOTIFICATIONS avec Laravel --}}
                            <li class="nav-item dropdown">
                                @if (Auth::check())
                                    <a class="nav-link px-2 icon-indicator icon-indicator-sm {{ auth()->user()->unreadNotifications->count() > 0 ? 'icon-indicator-danger' : '' }}"
                                        id="navbarTopDropdownNotification" href="#" role="button"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                                        aria-expanded="false">
                                        <span class="text-body-tertiary" data-feather="bell"
                                            style="height:20px;width:20px;"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret mt-2"
                                        id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                                        <div class="card position-relative border-0">
                                            <div class="card-header p-2">
                                                <div class="d-flex justify-content-between">
                                                    <h5 class="text-body-emphasis mb-0">Notifications
                                                        @if (auth()->user()->unreadNotifications->count() > 0)
                                                            <span
                                                                class="badge bg-danger ms-1">{{ auth()->user()->unreadNotifications->count() }}</span>
                                                        @endif
                                                    </h5>
                                                    <form method="POST"
                                                        action="{{ route('notifications.mark-all-read') }}"
                                                        class="d-inline" id="mark-all-read">
                                                        @csrf
                                                        <button class="btn btn-link p-0 fs-9 fw-normal"
                                                            type="submit">Tout
                                                            marquer lu</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="scrollbar-overlay" style="height: 27rem;">
                                                    @forelse(auth()->user()->notifications()->latest()->take(8)->get() as $notification)
                                                        <div
                                                            class="px-2 px-sm-3 py-3 notification-card position-relative {{ !$notification->read_at ? 'unread' : 'read' }} border-bottom">
                                                            <a href="{{ $notification->data['url'] ?? '#' }}"
                                                                class="text-decoration-none">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between position-relative">
                                                                    <div class="d-flex">
                                                                        {{-- Avatar --}}
                                                                        <div class="avatar avatar-m status-online me-3">
                                                                            @if ($notification->data['user_avatar'] ?? false)
                                                                                <img class="rounded-circle"
                                                                                    src="{{ $notification->data['user_avatar'] }}"
                                                                                    alt="" />
                                                                            @else
                                                                                <div class="avatar-name rounded-circle">
                                                                                    <span>{{ strtoupper(substr($notification->data['user_name'] ?? 'U', 0, 1)) }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="flex-1 me-sm-3">
                                                                            <h4 class="fs-9 text-body-emphasis mb-1">
                                                                                {{ $notification->data['user_name'] ?? 'Utilisateur' }}
                                                                            </h4>
                                                                            <p
                                                                                class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal">
                                                                                <span
                                                                                    class='me-1 fs-10'>{{ $notification->data['icon'] ?? '💬' }}</span>
                                                                                {{ $notification->data['message'] }}
                                                                                <span
                                                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">{{ $notification->created_at->diffForHumans() }}</span>
                                                                            </p>
                                                                            <p class="text-body-secondary fs-9 mb-0">
                                                                                <span class="me-1 fas fa-clock"></span>
                                                                                <span
                                                                                    class="fw-bold">{{ $notification->created_at->format('H:i') }}</span>
                                                                                {{ $notification->created_at->format('d M Y') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            {{-- Actions dropdown --}}
                                                            <div class="dropdown notification-dropdown">
                                                                <button
                                                                    class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                                                    type="button" data-bs-toggle="dropdown"
                                                                    data-boundary="window">
                                                                    <span
                                                                        class="fas fa-ellipsis-h fs-10 text-body"></span>
                                                                </button>
                                                                <div class="dropdown-menu py-2">
                                                                    @if ($notification->read_at)
                                                                        <form method="POST"
                                                                            action="{{ route('notifications.read', $notification->id) }}"
                                                                            class="d-inline">
                                                                            @csrf @method('PATCH')
                                                                            <button class="dropdown-item">Marquer non
                                                                                lu</button>
                                                                        </form>
                                                                    @else
                                                                        <form method="POST"
                                                                            action="{{ route('notifications.read', $notification->id) }}"
                                                                            class="d-inline">
                                                                            @csrf @method('PATCH')
                                                                            <button class="dropdown-item">Marquer
                                                                                lu</button>
                                                                        </form>
                                                                    @endif
                                                                    <form method="POST"
                                                                        action="{{ route('notifications.delete', $notification->id) }}"
                                                                        class="d-inline">
                                                                        @csrf @method('DELETE')
                                                                        <button
                                                                            class="dropdown-item text-danger">Supprimer</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-5 text-muted">
                                                            <i class="fas fa-bell-slash fs-3 mb-3 opacity-50"></i>
                                                            <p class="fs-9">Aucune notification</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div class="card-footer p-0 border-top border-translucent border-0">
                                                <div
                                                    class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85">
                                                    <a class="fw-bolder"
                                                        href="{{ route('notifications.index') }}">Historique
                                                        complet</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a class="nav-link px-2 icon-indicator icon-indicator-sm icon-indicator-danger"
                                        href="{{ route('login') }}">
                                        <span class="text-body-tertiary" data-feather="bell"
                                            style="height:20px;width:20px;"></span>
                                    </a>
                                @endif
                            </li>

                            {{-- profil --}}
                            <li class="nav-item dropdown">

                                @if (isset($user))
                                    <a class="nav-link px-2" id="navbarDropdownUser" href="#" role="button"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                                        aria-expanded="false"><span class="text-body-tertiary" data-feather="user"
                                            style="height:20px;width:20px;"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border mt-2"
                                        aria-labelledby="navbarDropdownUser">
                                        <div class="card position-relative border-0">
                                            <div class="card-body p-0">
                                                <div class="text-center pt-4 pb-3">
                                                    <div class="avatar avatar-xl ">
                                                        <img class="rounded-circle "
                                                            src="{{ $user?->getPhoto()?->getImageUrl(120, 120) ?? asset('assets/img/team/15.webp') }}"
                                                            alt="" />
                                                    </div>
                                                    <h6 class="mt-2 text-body-emphasis">{{ $user->name }}</h6>
                                                </div>
                                            </div>
                                            <div class="overflow-auto scrollbar" style="height: 10rem;">
                                                <ul class="nav d-flex flex-column mb-2 pb-1">
                                                    <li class="nav-item"><a class="nav-link px-3 d-block"
                                                            href="{{ route('dashboard') }}"><span
                                                                class="me-2 text-body align-bottom"
                                                                data-feather="pie-chart"></span>Dashboard</a></li>
                                                    <li class="nav-item"><a class="nav-link px-3 d-block"
                                                            href="#!">
                                                            <span class="me-2 text-body align-bottom"
                                                                data-feather="lock"></span>Posts &amp; Activity</a>
                                                    </li>
                                                    <li class="nav-item"><a class="nav-link px-3 d-block"
                                                            href="#!">
                                                            <span class="me-2 text-body align-bottom"
                                                                data-feather="settings"></span>Settings &amp; Privacy
                                                        </a></li>
                                                    <li class="nav-item"><a class="nav-link px-3 d-block"
                                                            href="#!">
                                                            <span class="me-2 text-body align-bottom"
                                                                data-feather="help-circle"></span>Help Center</a></li>
                                                    <li class="nav-item"><a class="nav-link px-3 d-block"
                                                            href="#!">
                                                            <span class="me-2 text-body align-bottom"
                                                                data-feather="globe"></span>Language</a></li>
                                                </ul>
                                            </div>
                                            <div class="card-footer p-0 border-top border-translucent">
                                                <ul class="nav d-flex flex-column my-3">
                                                    <li class="nav-item"><a class="nav-link px-3 d-block"
                                                            href="#!">
                                                            <span class="me-2 text-body align-bottom"
                                                                data-feather="user-plus"></span>Add another account</a>
                                                    </li>
                                                </ul>
                                                <hr />
                                                <div class="px-3">
                                                    <form method="POST" action="{{ route('logout') }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-phoenix-secondary d-flex flex-center w-100">
                                                            <span class="me-2" data-feather="log-out"></span>Sign
                                                            out
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="my-2 text-center fw-bold fs-10 text-body-quaternary"><a
                                                        class="text-body-quaternary me-1" href="#!">Privacy
                                                        policy</a>&bull;<a class="text-body-quaternary mx-1"
                                                        href="#!">Terms</a>&bull;<a
                                                        class="text-body-quaternary ms-1" href="#!">Cookies</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a class="nav-link px-2" href="{{ route('login') }}" role="button"
                                        aria-expanded="false"><span class="text-body-tertiary" data-feather="user"
                                            style="height:20px;width:20px;"></span>
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-6">
                        @livewire('search-ecommerce')

                    </div>
                </div>
            </nav>
        </div>
    </div><!-- end of .container-->
</section>
