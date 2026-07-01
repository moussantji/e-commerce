<nav class="navbar navbar-top navbar-slim justify-content-between fixed-top navbar-expand-lg" id="navbarTopSlim"
    style="display:none;">
    <div class="navbar-logo">
        <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse"
            aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span
                    class="toggle-line"></span></span></button>
        <a class="navbar-brand navbar-brand" href="index.html">phoenix <span
                class="text-body-highlight d-none d-sm-inline">slim</span></a>
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
    <ul class="navbar-nav navbar-nav-icons flex-row">
        <li class="nav-item">
            <div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input
                    class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox"
                    data-theme-control="phoenixTheme" value="dark" /><label
                    class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle"
                    data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span
                        class="d-none d-sm-flex flex-center" style="height:16px;width:16px;"><span class="me-1 icon"
                            data-feather="moon"></span></span><span class="fs-9 fw-bold">Dark</span></label><label
                    class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle"
                    data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span
                        class="d-none d-sm-flex flex-center" style="height:16px;width:16px;"><span class="me-1 icon"
                            data-feather="sun"></span></span><span class="fs-9 fw-bold">Light</span></label></div>
        </li>
        <li class="nav-item"> <a class="nav-link" href="#" data-bs-toggle="modal"
                data-bs-target="#searchBoxModal"><span class="d-inline-block" style="height:12px;width:12px;"><span
                        data-feather="search" style="height:12px;width:12px;"></span></span></a></li>
        <li class="nav-item dropdown">
            <a class="nav-link" id="navbarDropdownNotification" href="#" role="button"
                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                aria-expanded="false"><span class="d-inline-block" style="height:12px;width:12px;"><span
                        data-feather="bell" style="height:12px;width:12px;"></span></span></a>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret"
                id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                <div class="card position-relative border-0">
                    <div class="card-header p-2">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-body-emphasis mb-0">Notifications</h5><button
                                class="btn btn-link p-0 fs-9 fw-normal" type="button">Mark all as read</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="scrollbar-overlay" style="height: 27rem;">
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative read border-bottom">
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3"><img class="rounded-circle"
                                                src="assets/img/team/40x40/30.webp" alt="" /></div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs-9 text-body-emphasis">Jessie Samson</h4>
                                            <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                    class='me-1 fs-10'>💬</span>Mentioned you in a comment.<span
                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">10m</span>
                                            </p>
                                            <p class="text-body-secondary fs-9 mb-0"><span
                                                    class="me-1 fas fa-clock"></span><span class="fw-bold">10:41 AM
                                                </span>August 7,2021</p>
                                        </div>
                                    </div>
                                    <div class="dropdown notification-dropdown"><button
                                            class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false"
                                            data-bs-reference="parent"><span
                                                class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                        <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark
                                                as unread</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3">
                                            <div class="avatar-name rounded-circle"><span>J</span></div>
                                        </div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs-9 text-body-emphasis">Jane Foster</h4>
                                            <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                    class='me-1 fs-10'>📅</span>Created an event.<span
                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">20m</span>
                                            </p>
                                            <p class="text-body-secondary fs-9 mb-0"><span
                                                    class="me-1 fas fa-clock"></span><span class="fw-bold">10:20 AM
                                                </span>August 7,2021</p>
                                        </div>
                                    </div>
                                    <div class="dropdown notification-dropdown"><button
                                            class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false"
                                            data-bs-reference="parent"><span
                                                class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                        <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark
                                                as unread</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3"><img
                                                class="rounded-circle avatar-placeholder"
                                                src="assets/img/team/40x40/avatar.webp" alt="" /></div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs-9 text-body-emphasis">Jessie Samson</h4>
                                            <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                    class='me-1 fs-10'>👍</span>Liked your comment.<span
                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">1h</span>
                                            </p>
                                            <p class="text-body-secondary fs-9 mb-0"><span
                                                    class="me-1 fas fa-clock"></span><span class="fw-bold">9:30 AM
                                                </span>August 7,2021</p>
                                        </div>
                                    </div>
                                    <div class="dropdown notification-dropdown"><button
                                            class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false"
                                            data-bs-reference="parent"><span
                                                class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                        <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark
                                                as unread</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3"><img class="rounded-circle"
                                                src="assets/img/team/40x40/57.webp" alt="" /></div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs-9 text-body-emphasis">Kiera Anderson</h4>
                                            <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                    class='me-1 fs-10'>💬</span>Mentioned you in a comment.<span
                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10"></span>
                                            </p>
                                            <p class="text-body-secondary fs-9 mb-0"><span
                                                    class="me-1 fas fa-clock"></span><span class="fw-bold">9:11 AM
                                                </span>August 7,2021</p>
                                        </div>
                                    </div>
                                    <div class="dropdown notification-dropdown"><button
                                            class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false"
                                            data-bs-reference="parent"><span
                                                class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                        <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark
                                                as unread</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative unread border-bottom">
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3"><img class="rounded-circle"
                                                src="assets/img/team/40x40/59.webp" alt="" /></div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs-9 text-body-emphasis">Herman Carter</h4>
                                            <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                    class='me-1 fs-10'>👤</span>Tagged you in a comment.<span
                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10"></span>
                                            </p>
                                            <p class="text-body-secondary fs-9 mb-0"><span
                                                    class="me-1 fas fa-clock"></span><span class="fw-bold">10:58 PM
                                                </span>August 7,2021</p>
                                        </div>
                                    </div>
                                    <div class="dropdown notification-dropdown"><button
                                            class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false"
                                            data-bs-reference="parent"><span
                                                class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                        <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark
                                                as unread</a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 px-sm-3 py-3 notification-card position-relative read ">
                                <div class="d-flex align-items-center justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3"><img class="rounded-circle"
                                                src="assets/img/team/40x40/58.webp" alt="" /></div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs-9 text-body-emphasis">Benjamin Button</h4>
                                            <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                    class='me-1 fs-10'>👍</span>Liked your comment.<span
                                                    class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10"></span>
                                            </p>
                                            <p class="text-body-secondary fs-9 mb-0"><span
                                                    class="me-1 fas fa-clock"></span><span class="fw-bold">10:18 AM
                                                </span>August 7,2021</p>
                                        </div>
                                    </div>
                                    <div class="dropdown notification-dropdown"><button
                                            class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                            type="button" data-bs-toggle="dropdown" data-boundary="window"
                                            aria-haspopup="true" aria-expanded="false"
                                            data-bs-reference="parent"><span
                                                class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                        <div class="dropdown-menu py-2"><a class="dropdown-item" href="#!">Mark
                                                as unread</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-0 border-top border-translucent border-0">
                        <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85"><a
                                class="fw-bolder" href="pages/notifications.html">Notification history</a></div>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" id="navbarDropdownNindeDots" href="#" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside"
                aria-expanded="false"><svg width="10" height="10" viewbox="0 0 16 16" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="2" cy="2" r="2" fill="currentColor"></circle>
                    <circle cx="2" cy="8" r="2" fill="currentColor"></circle>
                    <circle cx="2" cy="14" r="2" fill="currentColor"></circle>
                    <circle cx="8" cy="8" r="2" fill="currentColor"></circle>
                    <circle cx="8" cy="14" r="2" fill="currentColor"></circle>
                    <circle cx="14" cy="8" r="2" fill="currentColor"></circle>
                    <circle cx="14" cy="14" r="2" fill="currentColor"></circle>
                    <circle cx="8" cy="2" r="2" fill="currentColor"></circle>
                    <circle cx="14" cy="2" r="2" fill="currentColor"></circle>
                </svg></a>
            <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-nine-dots shadow border"
                aria-labelledby="navbarDropdownNindeDots">
                <div class="card bg-body-emphasis position-relative border-0">
                    <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height: 20rem;">
                        <div class="row text-center align-items-center gx-0 gy-0">
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/behance.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Behance</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/google-cloud.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Cloud</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/slack.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Slack</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/gitlab.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Gitlab</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/bitbucket.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">BitBucket</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/google-drive.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Drive</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/trello.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Trello</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/figma.webp" alt=""
                                        width="20" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Figma</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/twitter.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Twitter</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/pinterest.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Pinterest</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/ln.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Linkedin</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/google-maps.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Maps</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/google-photos.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Photos</p>
                                </a></div>
                            <div class="col-4"><a
                                    class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                    href="#!"><img src="assets/img/nav-icons/spotify.webp" alt=""
                                        width="30" />
                                    <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Spotify</p>
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser"
                href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                data-bs-auto-close="outside" aria-expanded="false">{{ Auth::user()->name ?? 'Administrateur' }} <span class="d-inline-block"
                    style="height:10.2px;width:10.2px;"><span
                        class="fa-solid fa-chevron-down fs-10"></span></span></a>
            @include('admin.partials.profil.dropdown')
        </li>
    </ul>
</nav>
