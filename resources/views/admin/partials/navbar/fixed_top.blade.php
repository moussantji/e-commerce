<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault" style="display:none;">
    <div class="collapse navbar-collapse justify-content-between">
        <div class="navbar-logo">
            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse"
                aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span
                        class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="{{ route('admin.dashboard') }}">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center"><img src="{{ asset('assets/img/icons/logo.png') }}"
                            alt="phoenix" width="80" />
                        <h5 class="logo-text ms-2 d-none d-sm-block">MandenBaoubab</h5>
                    </div>
                </div>
            </a>
        </div>
        <ul class="navbar-nav navbar-nav-icons flex-row">
            <li class="nav-item">
                <div class="theme-control-toggle fa-icon-wait px-2"><input
                        class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                        data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label
                        class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle"
                        data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme"
                        style="height:32px;width:32px;"><span class="icon" data-feather="moon"></span></label><label
                        class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle"
                        data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme"
                        style="height:32px;width:32px;"><span class="icon" data-feather="sun"></span></label>
                </div>
            </li>
            <li class="nav-item d-lg-none">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span
                        class="d-block" style="height:20px;width:20px;"><span data-feather="search"
                            style="height:19px;width:19px;margin-bottom: 2px;"></span></span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-l">
                        <img class="rounded-circle"
                            src="{{ $user?->getPhoto()?->getImageUrl(120, 120) ?? asset('assets/img/team/15.webp') }}"
                            alt="Admin" />
                    </div>
                </a>
                @include('admin.partials.profil.dropdown')
            </li>
        </ul>
    </div>
</nav>
