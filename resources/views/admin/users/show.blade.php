@extends('admin.base')

@section('title', 'Utilisateur #' . $user->id)

@section('content')
    @php
        use App\Support\OrderStatus;
        $stClass = ['en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep', 'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko'];
    @endphp
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.users.index') }}">Clients</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ $user->name }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ $user->name }}</h1>
            <p>
                {!! ($user->status ?? '') === 'active' ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}
                <span class="muted-sm">{{ ucfirst($user->role) }} · inscrit le
                    {{ $user->created_at?->format('d/m/Y') }}</span>
            </p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-grid">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-user" />
                        </svg> Profil
                        <span
                            style="font-size:11.5px;font-weight:700;color:var(--violet-700)">{{ ucfirst($user->role) }}</span>
                    </h2>
                    <table class="spec">
                        <tbody>
                            <tr>
                                <td>Nom</td>
                                <td><b>{{ $user->name }}</b></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><a class="lien" href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                            </tr>
                            <tr>
                                <td>Téléphone</td>
                                <td>{{ $user->tel ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Ville</td>
                                <td>{{ $user->ville ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Dernière connexion</td>
                                <td>{{ $user->last_login ? $user->last_login->diffForHumans() : 'Jamais' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <form action="{{ route('admin.users.role', $user) }}" method="POST"
                        style="display:flex;align-items:center;gap:10px;margin-top:14px">
                        @csrf @method('PATCH')
                        <label class="muted-sm" for="role">Rôle :</label>
                        <select class="ctrl ctrl-sm" id="role" name="role" onchange="this.form.submit()">
                            <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Client</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>
                    <div class="pdp-actions" style="margin-top:16px">
                        <a class="btn-line" href="{{ route('admin.users.edit', $user) }}">Modifier</a>
                        <a class="btn-ghost-sm" href="{{ route('admin.users.index') }}">Retour</a>
                    </div>
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Commandes <span class="badge-nb">{{ $user->orders->count() }}</span></h2>
                    <div class="table-scroll">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->orders->sortByDesc('created_at') as $order)
                                    @php $norm = OrderStatus::normalize($order->statut); @endphp
                                    <tr>
                                        <td><b>{{ $order->numero_commande ?? 'CMD-' . $order->id }}</b></td>
                                        <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                        <td><span
                                                class="st {{ $stClass[$norm] ?? 'conf' }}">{{ $order->status_label ?? $order->statut }}</span>
                                        </td>
                                        <td><b>{{ number_format($order->total ?? 0, 0, ',', ' ') }} FCFA</b></td>
                                        <td><a class="btn-ghost-sm"
                                                href="{{ route('admin.orders.show', $order) }}">Voir</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="muted-sm">Aucune commande.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
