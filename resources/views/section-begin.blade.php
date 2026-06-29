<section class="py-0">
    <div class="container-small">
        <div class="ecommerce-topbar">
            <nav class="navbar navbar-expand-lg navbar-light px-0">
                <div class="row gx-0 gy-2 w-100 flex-between-center">
                    <div class="col-auto"><a class="text-decoration-none" href="{{ route('home') }}">
                            <div class="d-flex align-items-center"><img src="{{ asset('assets/img/icons/logo.png') }}"
                                    alt="phoenix" width="150" />
                            </div>
                        </a></div>
                    <div class="col-auto order-md-1">
                        <ul class="navbar-nav navbar-nav-icons flex-row me-n2">
                            {{-- light --}}
                            <li class="nav-item d-flex align-items-center">
                                <div class="theme-control-toggle fa-icon-wait px-2"><input
                                        class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                                        data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label
                                        class="mb-0 theme-control-toggle-label theme-control-toggle-light"
                                        for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="Switch theme" style="height:32px;width:32px;"><span class="icon"
                                            data-feather="moon"></span></label><label
                                        class="mb-0 theme-control-toggle-label theme-control-toggle-dark"
                                        for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="Switch theme" style="height:32px;width:32px;"><span class="icon"
                                            data-feather="sun"></span></label></div>
                            </li>
                            {{-- cart --}}
                            <!-- ✅ NOUVEAU -->
                            @livewire('navbar-cart-count')

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
                                            <div class="overflow-auto scrollbar" style="height: 5rem;">
                                                <div class="px-3">
                                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-phoenix-secondary d-flex flex-center w-100">
                                                            <span class="me-2" data-feather="log-out"></span>Sign
                                                            out
                                                        </button>
                                                    </form>
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
