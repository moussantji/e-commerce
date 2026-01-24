
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border"
                    aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="overflow-auto scrollbar  pt-4" style="max-height: 20rem;">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-flex align-items-center" href="{{ route('admin.profile') }}">
                                        <span class="me-2 text-body" data-feather="user"></span>
                                        <span>Mon Profil</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-flex align-items-center" href="{{ route('admin.users.index') }}">
                                        <span class="me-2 text-body" data-feather="settings"></span>
                                        <span>Paramètres</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer p-0 border-top border-translucent">
                            <div class="px-3 py-2">
                                <form method="POST" action="{{ route('logout') }}" class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-phoenix-secondary d-flex align-items-center w-100">
                                        <span class="me-2" data-feather="log-out"></span>
                                        <span>Déconnexion</span>
                                    </button>
                                </form>
                            </div>
                            <div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
                                <a class="text-body-quaternary me-1" href="{{ route('admin.privacy.policy') }}">Confidentialité</a>•
                                <a class="text-body-quaternary mx-1" href="{{ route('admin.terms') }}">Conditions</a>•
                                <a class="text-body-quaternary ms-1" href="{{ route('admin.contact') }}">Contact</a>
                            </div>
                        </div>
                    </div>
                </div>
