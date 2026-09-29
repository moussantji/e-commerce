@extends('base')

@section('title', 'Espace vendeur')

@section('content')
    @include('section-begin')

    @php
        use App\Support\OrderStatus;
        $stClass = ['en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep', 'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko'];
        $topMax = max(1, (int) ($topProduits->first()->vendus ?? 0));
    @endphp

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Espace vendeur</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Espace vendeur</h1>
            <p>Vos produits, vos commandes et vos revenus.</p>
            <div class="cats" style="padding:14px 0 0">
                <a class="cat hot" href="{{ route('vendeur.dashboard') }}">Tableau de bord</a>
                <a class="cat" href="{{ route('vendeur.products.index') }}">Mes produits</a>
                <a class="cat" href="{{ route('vendeur.orders.index') }}">Commandes reçues</a>
                <a class="cat" href="{{ route('dashboard') }}">Mon espace client</a>
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="kpis">
                <div class="kpi"><span class="kpi-l">Mes produits</span><b>{{ $nbProduits }}</b><span class="kpi-n">en
                        vente</span></div>
                <div class="kpi"><span class="kpi-l">Commandes reçues</span><b>{{ $nbCommandes }}</b><span
                        class="kpi-n">contenant vos produits</span></div>
                <div class="kpi"><span class="kpi-l">Revenus</span><b>{{ number_format($revenus, 0, ',', ' ') }}
                        FCFA</b><span class="kpi-n">total des ventes</span></div>
                <div class="kpi"><span class="kpi-l">Stock bas</span><b>{{ $stockBas }}</b><span class="kpi-n">produits à
                        ≤ 5 unités</span></div>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bag" />
                    </svg> Dernières commandes <span class="badge-nb">{{ $commandes->count() }}</span></h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>N°</th>
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
                                    <td><b>{{ $c->numero_commande ?? 'CMD-' . $c->id }}</b><br><small
                                            class="muted-sm">{{ $c->created_at?->format('d/m/Y') }}</small></td>
                                    <td>{{ optional($c->user)->name ?? '—' }}</td>
                                    <td>{{ $c->produits->where('vendeur_id', auth()->id())->map(fn($p) => Str::limit($p->name, 30) . ' ×' . ($p->pivot->quantite ?? 1))->join(' · ') }}
                                    </td>
                                    <td><b>{{ number_format($c->total, 0, ',', ' ') }} FCFA</b></td>
                                    <td><span
                                            class="st {{ $stClass[$norm] ?? 'conf' }}">{{ $c->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted-sm">Aucune commande pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pdp-actions" style="margin-top:14px">
                    <a class="btn-line" href="{{ route('vendeur.orders.index') }}">Toutes les commandes</a>
                    <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                        href="{{ route('vendeur.products.create') }}"><svg class="ic" style="width:16px;height:16px">
                            <use href="#i-b2-plus" />
                        </svg> Ajouter un produit</a>
                </div>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bolt" />
                    </svg> Mes meilleures ventes</h2>
                @forelse($topProduits as $i => $p)
                    <div class="top-prod">
                        <span class="rang">{{ $i + 1 }}</span>
                        <div class="tp-info"><b>{{ $p->name }}</b>
                            <div class="bar"><i style="width:{{ round($p->vendus / $topMax * 100) }}%"></i></div>
                        </div>
                        <span class="tp-nb">{{ $p->vendus }}</span>
                    </div>
                @empty
                    <p class="muted-sm">Aucune vente pour le moment.</p>
                @endforelse
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
