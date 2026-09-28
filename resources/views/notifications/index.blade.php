@extends('base')

@section('title', 'Notifications')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('dashboard') }}">Mon espace client</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Notifications</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Notifications ({{ auth()->user()->notifications->count() }})</h1>
            <p>Suivi de commandes, paiements et nouveautés.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow" style="max-width:760px">
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <div class="pdp-actions" style="margin-bottom:14px">
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="display:inline">
                            @csrf
                            <button class="btn-line" type="submit">Tout marquer lu</button>
                        </form>
                    </div>
                @endif

                @forelse(auth()->user()->notifications()->latest()->get()->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        }) as $date => $notifications)
                    <h3 class="h3-mini" style="margin:18px 0 8px">
                        {{ $date == today()->format('Y-m-d') ? "Aujourd'hui" : ($date == yesterday()->format('Y-m-d') ? 'Hier' : $date) }}
                    </h3>
                    <div class="panel" style="padding:6px 18px">
                        @foreach ($notifications as $notification)
                            <div class="dligne" @if (!$notification->read_at) style="font-weight:600" @endif>
                                <span
                                    style="width:44px;height:44px;border-radius:50%;display:grid;place-items:center;font-weight:800;font-size:17px;color:#fff;background:linear-gradient(135deg,var(--violet-600),var(--violet-400));flex:none">{{ strtoupper(substr($notification->data['user_name'] ?? 'B', 0, 1)) }}</span>
                                <div style="flex:1;min-width:0">
                                    <a
                                        href="{{ \App\Support\NotificationLink::url($notification->data) }}"><b>{{ \App\Support\NotificationLink::title($notification->data) }}</b></a>
                                    <small style="display:block">{{ $notification->data['icon'] ?? '🔔' }}
                                        {{ \App\Support\NotificationLink::message($notification->data) }}</small>
                                    <small
                                        class="muted-sm">{{ $notification->created_at->diffForHumans() }} ·
                                        {{ $notification->created_at->format('H:i') }}</small>
                                </div>
                                <span style="display:flex;gap:6px;flex:none">
                                    <form method="POST"
                                        action="{{ route('notifications.read', $notification->id) }}"
                                        style="display:inline">
                                        @csrf @method('PATCH')
                                        <button class="btn-ghost-sm" type="submit"
                                            title="{{ $notification->read_at ? 'Marquer non lu' : 'Marquer lu' }}">{{ $notification->read_at ? 'Non lu' : 'Lu' }}</button>
                                    </form>
                                    <form method="POST"
                                        action="{{ route('notifications.delete', $notification->id) }}"
                                        style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn-ghost-sm" type="submit" style="color:var(--pink)"
                                            title="Supprimer">×</button>
                                    </form>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="empty"><svg class="ic">
                            <use href="#i-headset" />
                        </svg>
                        <h3>Aucune notification</h3>
                        <p>Vous serez notifié dès qu'il y aura du nouveau.</p>
                        <div class="pdp-actions" style="justify-content:center;margin-top:20px">
                            <a class="btn-solid" href="{{ route('products') }}">Voir les produits</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
