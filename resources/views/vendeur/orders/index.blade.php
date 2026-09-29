@extends('base')

@section('title', 'Commandes reçues — Vendeur')

@section('content')
    @include('section-begin')

    @php
        use App\Support\OrderStatus;
        $stClass = ['en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep', 'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko'];
    @endphp

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('vendeur.dashboard') }}">Espace vendeur</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Commandes reçues</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Commandes reçues</h1>
            <p>Commandes contenant vos produits.</p>
            <div class="cats" style="padding:14px 0 0">
                <a class="cat" href="{{ route('vendeur.dashboard') }}">Tableau de bord</a>
                <a class="cat" href="{{ route('vendeur.products.index') }}">Mes produits</a>
                <a class="cat hot" href="{{ route('vendeur.orders.index') }}">Commandes reçues</a>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bag" />
                    </svg> Commandes ({{ $commandes->total() }})</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Vos produits</th>
                                <th>Total cmd.</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commandes as $c)
                                @php $norm = OrderStatus::normalize($c->statut); @endphp
                                <tr>
                                    <td><b>{{ $c->numero_commande ?? 'CMD-' . $c->id }}</b></td>
                                    <td>{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>{{ optional($c->user)->name ?? '—' }}<br><small
                                            class="muted-sm">{{ optional($c->user)->tel ?? '' }}</small></td>
                                    <td>{{ $c->produits->where('vendeur_id', auth()->id())->map(fn($p) => Str::limit($p->name, 30) . ' ×' . ($p->pivot->quantite ?? 1))->join(' · ') }}
                                    </td>
                                    <td><b>{{ number_format($c->total, 0, ',', ' ') }} FCFA</b></td>
                                    <td><span
                                            class="st {{ $stClass[$norm] ?? 'conf' }}">{{ $c->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted-sm">Aucune commande reçue.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($commandes->hasPages())
                    <div class="pager">
                        @if ($commandes->onFirstPage())
                            <button disabled>‹</button>
                        @else
                            <a href="{{ $commandes->previousPageUrl() }}"><button type="button">‹</button></a>
                        @endif
                        <button class="on">{{ $commandes->currentPage() }}</button>
                        @if ($commandes->hasMorePages())
                            <a href="{{ $commandes->nextPageUrl() }}"><button type="button">›</button></a>
                        @else
                            <button disabled>›</button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
