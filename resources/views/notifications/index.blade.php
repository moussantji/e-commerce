@extends('base')

@section('content')
    <!-- ============================================-->
    <!-- <section> begin ============================-->
    @include('section-begin')
    <!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.nav')

    <section class="content pt-5 pb-9">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Notifications</li>
            </ol>
        </nav>

        <h2 class="mb-5">Notifications ({{ auth()->user()->notifications->count() }})</h2>

        {{-- Bouton tout marquer lu --}}
        @if (auth()->user()->unreadNotifications->count() > 0)
            <div class="mb-4">
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-primary">Tout marquer lu</button>
                </form>
            </div>
        @endif

        @forelse(auth()->user()->notifications()->latest()->get()->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        }) as $date => $notifications)
            <h5 class="text-body-emphasis mb-3">
                {{ $date == today()->format('Y-m-d') ? 'Aujourd\'hui' : ($date == yesterday()->format('Y-m-d') ? 'Hier' : $date) }}
            </h5>

            <div class="mx-n4 mx-lg-n6 mb-5 border-bottom">
                @foreach ($notifications as $notification)
                    <div
                        class="d-flex align-items-center justify-content-between py-3 px-lg-6 px-4 notification-card border-top {{ !$notification->read_at ? 'unread' : 'read' }}">
                        <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none">
                            <div class="d-flex">
                                {{-- Avatar --}}
                                <div class="avatar avatar-xl me-3">
                                    @if (isset($notification->data['user_avatar']))
                                        <img class="rounded-circle" src="{{ $notification->data['user_avatar'] }}"
                                            alt="">
                                    @else
                                        <div class="avatar-name rounded-circle">
                                            <span>{{ strtoupper(substr($notification->data['user_name'] ?? 'U', 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="me-3 flex-1 mt-2">
                                    <h4 class="fs-9 text-body-emphasis">{{ $notification->data['user_name'] ?? 'Système' }}
                                    </h4>
                                    <p class="fs-9 text-body-highlight">
                                        <span class='me-1'>{{ $notification->data['icon'] ?? '🔔' }}</span>
                                        {{ $notification->data['message'] }}
                                        <span class="ms-2 text-body-tertiary text-opacity-85 fw-bold fs-10">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </p>
                                    <p class="text-body-secondary fs-9 mb-0">
                                        <span class="me-1 fas fa-clock"></span>
                                        <span class="fw-bold">{{ $notification->created_at->format('H:i') }}</span>
                                        {{ $notification->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </a>

                        {{-- Actions --}}
                        <div class="dropdown">
                            <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                data-bs-toggle="dropdown">
                                <span class="fas fa-ellipsis-h fs-10 text-body"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end py-2">
                                @if ($notification->read_at)
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                                        class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="dropdown-item">Marquer non lu</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                                        class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="dropdown-item">Marquer lu</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('notifications.delete', $notification->id) }}"
                                    class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-center py-8">
                <i class="fas fa-bell-slash fs-1 text-muted mb-4"></i>
                <h5 class="text-muted">Aucune notification</h5>
                <p class="text-muted">Vous serez notifié dès qu'il y aura du nouveau</p>
            </div>
        @endforelse
    </section>



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
                            class="btn btn-link d-flex p-0 text-body-quaternary fs-9 border-0"
                            for="supportChatPhotos"><span class="fa-solid fa-image"></span></label><input class="d-none"
                            type="file" accept="image/*" id="supportChatPhotos" /><label
                            class="btn btn-link d-flex p-0 text-body-quaternary fs-9 border-0"
                            for="supportChatAttachment">
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
                    <div class="d-flex align-items-center mb-3"><img src="../../../assets/img/icons/logo.png"
                            alt="phoenix" width="27" />
                        <h5 class="logo-text ms-2">phoenix</h5>
                    </div>
                    <p class="text-body-tertiary mb-1 fw-semibold lh-sm fs-9">Phoenix is an admin dashboard template with
                        fascinating features and amazing layout. The template is responsive to all major browsers and is
                        compatible with all available devices and screen sizes.</p>
                </div>
                <div class="col-6 col-md-auto">
                    <h5 class="fw-bolder mb-3">About Phoenix</h5>
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1"
                            href="#!">Careers</a><a class="text-body-tertiary fw-semibold fs-9 mb-1"
                            href="#!">Affiliate
                            Program</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Privacy
                            Policy</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Terms &
                            Conditions</a></div>
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
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1"
                            href="#!">Help
                            Desk</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Support, 24/7</a><a
                            class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Community of Phoenix</a></div>
                </div>
                <div class="col-6 col-md-auto">
                    <h5 class="fw-bolder mb-3">Payment Method</h5>
                    <div class="d-flex flex-column"><a class="text-body-tertiary fw-semibold fs-9 mb-1"
                            href="#!">Cash on
                            Delivery</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">Online
                            Payment</a><a class="text-body-tertiary fw-semibold fs-9 mb-1" href="#!">PayPal</a><a
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
