@extends('base')

@section('content')
    <!-- ============================================-->
    <!-- <section> begin ============================-->
    @include('section-begin')
    <!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.nav')


    <!-- ============================================-->
    <!-- <section> begin ============================-->
    <section class="pt-5 pb-9">
        <div class="container-small cart">
            <nav class="mb-3" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home me-1"></i>Acceuil</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-heart me-1"></i>Wishlist</li>
                </ol>
            </nav>
            @livewire('whishlist')
        </div><!-- end of .container-->
    </section><!-- <section> close ============================-->
    <!-- ============================================-->


    <div class="support-chat-container">
        <div class="container-fluid support-chat">
            <div class="card bg-body-emphasis">
                <div class="card-header d-flex flex-between-center px-4 py-3 border-bottom border-translucent">
                    <h5 class="mb-0 d-flex align-items-center gap-2">Demo widget<span
                            class="fa-solid fa-circle text-success fs-11"></span></h5>
                    <div class="btn-reveal-trigger"><button
                            class="btn btn-link p-0 dropdown-toggle dropdown-caret-none transition-none d-flex"
                            type="button" id="support-chat-dropdown" data-bs-toggle="dropdown" data-boundary="window"
                            aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span
                                class="fas fa-ellipsis-h text-body"></span></button>
                        <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown"><a
                                class="dropdown-item" href="#!">Request a callback</a><a class="dropdown-item"
                                href="#!">Search in chat</a><a class="dropdown-item" href="#!">Show
                                history</a><a class="dropdown-item" href="#!">Report to Admin</a><a
                                class="dropdown-item btn-support-chat" href="#!">Close Support</a></div>
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
                                <p class="mb-0 fw-semibold fs-9">I can’t reorder a product I previously ordered</p><span
                                    class="fa-solid fa-paper-plane text-primary fs-9 ms-3"></span>
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
                            <p class="text-center text-body-emphasis mb-0">Ask us anything – we’ll get back to you here or
                                by email within 24 hours.</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center gap-2 border-top border-translucent ps-3 pe-4 py-3">
                    <div class="d-flex align-items-center flex-1 gap-3 border border-translucent rounded-pill px-4"><input
                            class="form-control outline-none border-0 flex-1 fs-9 px-0" type="text"
                            placeholder="Write message" /><label
                            class="btn btn-link d-flex p-0 text-body-quaternary fs-9 border-0" for="supportChatPhotos"><span
                                class="fa-solid fa-image"></span></label><input class="d-none" type="file" accept="image/*"
                            id="supportChatPhotos" /><label
                            class="btn btn-link d-flex p-0 text-body-quaternary fs-9 border-0" for="supportChatAttachment">
                            <span class="fa-solid fa-paperclip"></span></label><input class="d-none" type="file"
                            id="supportChatAttachment" /></div><button class="btn p-0 border-0 send-btn"><span
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

    <!-- ============================================-->
    <!-- <section> begin ============================-->
    <section class="bg-body-highlight dark__bg-gray-1100 py-9">
        <div class="container-small">
            <div class="row justify-content-between gy-4">
                <div class="col-12 col-lg-4">
                    <div class="d-flex align-items-center mb-3"><img src="../../../assets/img/icons/logo.png" alt="phoenix"
                            width="27" />
                        <h5 class="logo-text ms-2">phoenix</h5>
                    </div>
                    <p class="text-body-tertiary mb-1 fw-semibold lh-sm fs-9">Phoenix is an admin dashboard template with
                        fascinating features and amazing layout. The template is responsive to all major browsers and is
                        compatible with all available devices and screen sizes.</p>
                </div>
                <div class="col-6 col-md-auto">
                    <h5 class="fw-bolder mb-3">About Phoenix</h5>
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1"
                            href="#!">Careers</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Affiliate
                            Program</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Privacy Policy</a><a
                            class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Terms & Conditions</a></div>
                </div>
                <div class="col-6 col-md-auto">
                    <h5 class="fw-bolder mb-3">Stay Connected</h5>
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1"
                            href="#!">Blogs</a><a class="mb-1 fw-semibold fs-9 d-flex" href="#!"><span
                                class="fab fa-facebook-square text-primary me-2 fs-8"></span><span
                                class="text-body-secondary">Facebook</span></a><a class="mb-1 fw-semibold fs-9 d-flex"
                            href="#!"><span class="fab fa-twitter-square text-info me-2 fs-8"></span><span
                                class="text-body-secondary">Twitter</span></a></div>
                </div>
                <div class="col-6 col-md-auto">
                    <h5 class="fw-bolder mb-3">Customer Service</h5>
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Help
                            Desk</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Support, 24/7</a><a
                            class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Community of Phoenix</a></div>
                </div>
                <div class="col-6 col-md-auto">
                    <h5 class="fw-bolder mb-3">Payment Method</h5>
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Cash on
                            Delivery</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Online Payment</a><a
                            class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">PayPal</a><a
                            class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Installment</a></div>
                </div>
            </div>
        </div><!-- end of .container-->
    </section><!-- <section> close ============================-->
    <!-- ============================================-->

    <footer class="footer position-relative">
        <div class="row g-0 justify-content-between align-items-center h-100">
            <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 mt-2 mt-sm-0 text-body">Thank you for creating with Phoenix<span
                        class="d-none d-sm-inline-block"></span><span class="d-none d-sm-inline-block mx-1">|</span><br
                        class="d-sm-none" />2025 &copy;<a class="mx-1" href="https://themewagon.com/">Themewagon</a>
                </p>
            </div>
            <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 text-body-tertiary text-opacity-85">v1.23.0</p>
            </div>
        </div>
    </footer>
@endsection
