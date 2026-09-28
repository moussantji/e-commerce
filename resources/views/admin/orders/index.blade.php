@extends('admin.base')

@section('title', 'Gestion des commandes')

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
            <span class="here">Commandes</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Commandes</h1>
            <p>{{ $commandes->count() }} commande{{ $commandes->count() > 1 ? 's' : '' }} · suivi et statuts.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <form action="{{ route('admin.orders.index') }}" method="GET"
                    style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1">
                    <input class="ctrl" style="border-radius:12px;min-width:200px" type="search" name="search"
                        placeholder="N°, client, email..." value="{{ request('search') }}" aria-label="Rechercher">
                    <select class="ctrl" name="status" onchange="this.form.submit()" aria-label="Filtrer par statut">
                        <option value="">Tous les statuts</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @if (request('search') || request('status'))
                        <a class="btn-ghost-sm" href="{{ route('admin.orders.index') }}">Effacer</a>
                    @endif
                </form>
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.payments.moderation') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-card" />
                    </svg> Paiements à vérifier</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bag" />
                    </svg> Liste des commandes</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Paiement</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commandes as $c)
                                @php $norm = OrderStatus::normalize($c->statut); @endphp
                                <tr>
                                    <td><b>{{ $c->numero_commande ?? 'CMD-' . $c->id }}</b></td>
                                    <td>{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($c->user)->name ?? '—' }}<br><small
                                            class="muted-sm">{{ optional($c->user)->email ?? '' }}</small></td>
                                    <td>{{ optional($c->paiement)->method_name ?? '—' }}</td>
                                    <td><b>{{ number_format($c->total, 0, ',', ' ') }} FCFA</b></td>
                                    <td>
                                        <form action="{{ route('admin.orders.update-status', $c->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <select class="ctrl ctrl-sm" name="status" onchange="this.form.submit()"
                                                aria-label="Changer le statut">
                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ $norm === $value ? 'selected' : '' }}>{{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <span class="st {{ $stClass[$norm] ?? 'conf' }}"
                                            style="margin-top:6px">{{ $c->status_label }}</span>
                                    </td>
                                    <td><a class="btn-ghost-sm"
                                            href="{{ route('admin.orders.show', $c->id) }}">Voir</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted-sm">Aucune commande.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
