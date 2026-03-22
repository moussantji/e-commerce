<li class="nav-item dropdown">
    @if (Auth::check())
        <a class="nav-link px-2 icon-indicator icon-indicator-sm {{ auth()->user()->unreadNotifications->count() > 0 ? 'icon-indicator-danger' : '' }}"
            id="navbarTopDropdownNotification" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            aria-haspopup="true" aria-expanded="false">
            <span class="text-body-tertiary" data-feather="bell" style="height:20px;width:20px;"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret mt-2"
            id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
            <div class="card position-relative border-0">
                <div class="card-header p-2">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-body-emphasis mb-0">Notifications
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger ms-1">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </h5>
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline"
                            id="mark-all-read">
                            @csrf
                            <button class="btn btn-link p-0 fs-9 fw-normal" type="submit">Tout
                                marquer lu</button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="scrollbar-overlay" style="height: 27rem;">
                        @forelse(auth()->user()->notifications()->latest()->take(8)->get() as $notification)
                            <div
                                class="px-2 px-sm-3 py-3 notification-card position-relative {{ !$notification->read_at ? 'unread' : 'read' }} border-bottom">
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                            {{-- Avatar --}}
                                            <div class="avatar avatar-m status-online me-3">
                                                @if ($notification->data['user_avatar'] ?? false)
                                                    <img class="rounded-circle" src="{{ $notification->data['user_avatar'] }}"
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
                                                <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal">
                                                    <span class='me-1 fs-10'>{{ $notification->data['icon'] ?? '💬' }}</span>
                                                    {{ $notification->data['message'] }}
                                                    <span
                                                        class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">{{ $notification->created_at->diffForHumans() }}</span>
                                                </p>
                                                <p class="text-body-secondary fs-9 mb-0">
                                                    <span class="me-1 fas fa-clock"></span>
                                                    <span class="fw-bold">{{ $notification->created_at->format('H:i') }}</span>
                                                    {{ $notification->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                {{-- Actions dropdown --}}
                                <div class="dropdown notification-dropdown">
                                    <button class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                        type="button" data-bs-toggle="dropdown" data-boundary="window">
                                        <span class="fas fa-ellipsis-h fs-10 text-body"></span>
                                    </button>
                                    <div class="dropdown-menu py-2">
                                        @if ($notification->read_at)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                                                class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="dropdown-item">Marquer non
                                                    lu</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}"
                                                class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="dropdown-item">Marquer
                                                    lu</button>
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
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-bell-slash fs-3 mb-3 opacity-50"></i>
                                <p class="fs-9">Aucune notification</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer p-0 border-top border-translucent border-0">
                    <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85">
                        <a class="fw-bolder" href="{{ route('notifications.index') }}">Historique
                            complet</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <a class="nav-link px-2 icon-indicator icon-indicator-sm icon-indicator-danger" href="{{ route('login') }}">
            <span class="text-body-tertiary" data-feather="bell" style="height:20px;width:20px;"></span>
        </a>
    @endif
</li>
