<x-guest-layout>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
            <div class="bg-holder bg-auth-card-overlay" style="background-image:url(../../../assets/img/bg/37.png);">
            </div>
            <!--/.bg-holder-->
            <div class="row flex-center position-relative min-vh-100 g-0 py-5">
                <div class="col-11 col-sm-10 col-xl-8">
                    <div class="card border border-translucent auth-card">
                        <div class="card-body pe-md-0">
                            <div class="row align-items-center gx-0 gy-7">
                                <div
                                    class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
                                    <div class="bg-holder" style="background-image:url(../../../assets/img/bg/38.png);">
                                    </div>
                                    <!--/.bg-holder-->
                                    <div
                                        class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 card-sign-up">
                                        <h3 class="mb-3 text-body-emphasis fs-7">Phoenix Authentication</h3>
                                        <p class="text-body-tertiary">Give yourself some hassle-free development process
                                            with the uniqueness of Phoenix!</p>
                                        <ul class="list-unstyled mb-0 w-max-content w-md-auto">
                                            <li class="d-flex align-items-center"><span
                                                    class="uil uil-check-circle text-success me-2"></span><span
                                                    class="text-body-tertiary fw-semibold">Fast</span></li>
                                            <li class="d-flex align-items-center"><span
                                                    class="uil uil-check-circle text-success me-2"></span><span
                                                    class="text-body-tertiary fw-semibold">Simple</span></li>
                                            <li class="d-flex align-items-center"><span
                                                    class="uil uil-check-circle text-success me-2"></span><span
                                                    class="text-body-tertiary fw-semibold">Responsive</span></li>
                                        </ul>
                                    </div>
                                    <div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15"><img
                                            class="auth-title-box-img d-dark-none"
                                            src="../../../assets/img/spot-illustrations/auth.png" alt="" /><img
                                            class="auth-title-box-img d-light-none"
                                            src="../../../assets/img/spot-illustrations/auth-dark.png" alt="" />
                                    </div>
                                </div>
                                <div class="col mx-auto">
                                    <div class="auth-form-box text-center">
                                        <!-- Logo -->
                                        <a class="d-flex flex-center text-decoration-none mb-4"
                                            href="{{ route('dashboard') }}">
                                            <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                                                <img src="{{ asset('assets/img/icons/logo.png') }}" alt="Phoenix"
                                                    width="58" />
                                            </div>
                                        </a>

                                        <!-- Icône vérification -->
                                        <div class="mb-4">
                                            <div class="icon-circle bg-primary-soft text-primary mx-auto mb-3">
                                                <span class="uil uil-envelope-check fs-2"></span>
                                            </div>
                                        </div>

                                        <!-- Titre -->
                                        <h4 class="text-body-highlight mb-2">{{ __('Thanks for signing up!') }}</h4>
                                        <p class="text-body-tertiary mb-4 fs-9">
                                            {{ __('Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                                        </p>

                                        <!-- Status succès -->
                                        @if (session('status') == 'verification-link-sent')
                                            <div class="alert alert-success alert-dismissible fade show mb-4"
                                                role="alert">
                                                <span class="uil uil-check-circle me-2"></span>
                                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif

                                        <!-- Actions -->
                                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                                            <!-- Resend Email -->
                                            <form method="POST" action="{{ route('verification.send') }}"
                                                class="d-inline">
                                                @csrf
                                                <button class="btn btn-outline-primary btn-sm px-4">
                                                    <span class="uil uil-redo me-1"></span>
                                                    {{ __('Resend Verification Email') }}
                                                </button>
                                            </form>

                                            <!-- Logout -->
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-link text-decoration-none p-0 text-body-tertiary fs-9 fw-semibold">
                                                    <span class="uil uil-signout me-1"></span>
                                                    {{ __('Log Out') }}
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Compteur (optionnel) -->
                                        <div class="mt-4 p-3 bg-light rounded-2">
                                            <small class="text-body-tertiary">
                                                {{ __('If you did not receive the email, please check your spam folder.') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="support-chat-container">
            <div class="container-fluid support-chat">
                <div class="card bg-body-emphasis">
                    <div class="card-header d-flex flex-between-center px-4 py-3 border-bottom border-translucent">
                        <h5 class="mb-0 d-flex align-items-center gap-2">Demo widget<span
                                class="fa-solid fa-circle text-success fs-11"></span></h5>
                        <div class="btn-reveal-trigger"><button
                                class="btn btn-link p-0 dropdown-toggle dropdown-caret-none transition-none d-flex"
                                type="button" id="support-chat-dropdown" data-bs-toggle="dropdown"
                                data-boundary="window" aria-haspopup="true" aria-expanded="false"
                                data-bs-reference="parent"><span class="fas fa-ellipsis-h text-body"></span></button>
                            <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown">
                                <a class="dropdown-item" href="#!">Request a callback</a><a class="dropdown-item"
                                    href="#!">Search in chat</a><a class="dropdown-item" href="#!">Show
                                    history</a><a class="dropdown-item" href="#!">Report to
                                    Admin</a><a class="dropdown-item btn-support-chat" href="#!">Close
                                    Support</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body chat p-0">
                        <div class="d-flex flex-column-reverse scrollbar h-100 p-3">
                            <div class="text-end mt-6"><a
                                    class="mb-2 d-inline-flex align-items-center text-decoration-none text-body-emphasis bg-body-hover rounded-pill border border-primary py-2 ps-4 pe-3"
                                    href="#!">
                                    <p class="mb-0 fw-semibold fs-9">I need help with something</p><span
                                        class="fa-solid fa-paper-plane text-primary fs-9 ms-3"></span>
                                </a><a
                                    class="mb-2 d-inline-flex align-items-center text-decoration-none text-body-emphasis bg-body-hover rounded-pill border border-primary py-2 ps-4 pe-3"
                                    href="#!">
                                    <p class="mb-0 fw-semibold fs-9">I can’t reorder a product I previously ordered</p>
                                    <span class="fa-solid fa-paper-plane text-primary fs-9 ms-3"></span>
                                </a><a
                                    class="mb-2 d-inline-flex align-items-center text-decoration-none text-body-emphasis bg-body-hover rounded-pill border border-primary py-2 ps-4 pe-3"
                                    href="#!">
                                    <p class="mb-0 fw-semibold fs-9">How do I place an order?</p><span
                                        class="fa-solid fa-paper-plane text-primary fs-9 ms-3"></span>
                                </a><a
                                    class="false d-inline-flex align-items-center text-decoration-none text-body-emphasis bg-body-hover rounded-pill border border-primary py-2 ps-4 pe-3"
                                    href="#!">
                                    <p class="mb-0 fw-semibold fs-9">My payment method not working</p><span
                                        class="fa-solid fa-paper-plane text-primary fs-9 ms-3"></span>
                                </a></div>
                            <div class="text-center mt-auto">
                                <div class="avatar avatar-3xl status-online"><img
                                        class="rounded-circle border border-3 border-light-subtle"
                                        src="../../../assets/img/team/30.webp" alt="" /></div>
                                <h5 class="mt-2 mb-3">Eric</h5>
                                <p class="text-center text-body-emphasis mb-0">Ask us anything – we’ll get back to you
                                    here or by email within 24 hours.</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="card-footer d-flex align-items-center gap-2 border-top border-translucent ps-3 pe-4 py-3">
                        <div
                            class="d-flex align-items-center flex-1 gap-3 border border-translucent rounded-pill px-4">
                            <input class="form-control outline-none border-0 flex-1 fs-9 px-0" type="text"
                                placeholder="Write message" /><label
                                class="btn btn-link d-flex p-0 text-body-quaternary fs-9 border-0"
                                for="supportChatPhotos"><span class="fa-solid fa-image"></span></label><input
                                class="d-none" type="file" accept="image/*" id="supportChatPhotos" /><label
                                class="btn btn-link d-flex p-0 text-body-quaternary fs-9 border-0"
                                for="supportChatAttachment"> <span class="fa-solid fa-paperclip"></span></label><input
                                class="d-none" type="file" id="supportChatAttachment" />
                        </div><button class="btn p-0 border-0 send-btn"><span
                                class="fa-solid fa-paper-plane fs-9"></span></button>
                    </div>
                </div>
            </div><button class="btn btn-support-chat p-0 border border-translucent"><span
                    class="fs-8 btn-text text-primary text-nowrap">Chat demo</span><span
                    class="ping-icon-wrapper mt-n4 ms-n6 mt-sm-0 ms-sm-2 position-absolute position-sm-relative"><span
                        class="ping-icon-bg"></span><span class="fa-solid fa-circle ping-icon"></span></span><span
                    class="fa-solid fa-headset text-primary fs-8 d-sm-none"></span><span
                    class="fa-solid fa-chevron-down text-primary fs-7"></span></button>
        </div>
    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

</x-guest-layout>
