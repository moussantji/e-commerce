@extends('admin.base')

@section('title', 'Tableau de bord — Administration')

@section('content')
    @php
        use App\Support\OrderStatus;
        $alertesStock = \App\Models\Produits::where('stock', '<=', 5)->count();
        $topVentes = \App\Models\Produits::select('produits.id', 'produits.name', \DB::raw('COALESCE(SUM(commande_produit.quantite),0) as vendus'))
            ->leftJoin('commande_produit', 'commande_produit.produit_id', '=', 'produits.id')
            ->groupBy('produits.id', 'produits.name')
            ->orderByDesc('vendus')
            ->take(5)
            ->get();
        $topMax = max(1, (int) ($topVentes->first()->vendus ?? 0));
        $derniersClients = \App\Models\User::where('role', 'customer')->latest()->take(5)->get();
        $stClass = ['en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep', 'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko'];
    @endphp

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Administration</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Tableau de bord — Administration</h1>
            <p>Ventes, commandes, stocks et clients.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="kpis">
                <div class="kpi"><span class="kpi-l">Chiffre d'affaires</span><b>{{ $totalSales ?? '0 FCFA' }}</b><span
                        class="kpi-n">{{ $totalOrders ?? 0 }} commandes au total</span></div>
                <div class="kpi"><span class="kpi-l">En attente</span><b>{{ $pendingOrders ?? 0 }}</b><span
                        class="kpi-n">paiements à vérifier</span></div>
                <div class="kpi"><span class="kpi-l">Payées</span><b>{{ $processingOrders ?? 0 }}</b><span
                        class="kpi-n">prêtes à expédier</span></div>
                <div class="kpi"><span class="kpi-l">Alertes stock</span><b>{{ $alertesStock }}</b><span
                        class="kpi-n">produits à ≤ 5 unités</span></div>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bag" />
                    </svg> Commandes en attente <span
                        class="badge-nb">{{ isset($commandes) ? $commandes->count() : 0 }}</span></h2>
                @if (!empty($commandes) && $commandes->count())
                    <div class="table-scroll">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($commandes as $c)
                                    @php $norm = OrderStatus::normalize($c->statut); @endphp
                                    <tr>
                                        <td><b>{{ $c->numero_commande ?? 'CMD-' . $c->id }}</b></td>
                                        <td>{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>{{ optional($c->user)->name ?? '—' }}<br><small
                                                class="muted-sm">{{ optional($c->user)->email ?? '' }}</small></td>
                                        <td><b>{{ number_format($c->total, 0, ',', ' ') }} FCFA</b></td>
                                        <td><span class="st {{ $stClass[$norm] ?? 'conf' }}">{{ $c->status_label }}</span>
                                        </td>
                                        <td><a class="btn-ghost-sm"
                                                href="{{ route('admin.orders.show', $c->id) }}">Voir</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="muted-sm">Aucune commande en attente.</p>
                @endif
                <div class="pdp-actions" style="margin-top:14px">
                    <a class="btn-line" href="{{ route('admin.orders.index') }}"><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Toutes les commandes</a>
                </div>
            </div>

            <div class="dash-grid">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-store" />
                        </svg> Stocks faibles</h2>
                    @php $faibles = \App\Models\Produits::with('category')->where('stock', '<=', 5)->orderBy('stock')->take(8)->get(); @endphp
                    @if ($faibles->count())
                        <div class="table-scroll">
                            <table class="tbl">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Stock</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faibles as $p)
                                        @php $cls = ($p->stock ?? 0) <= 0 ? 'out' : 'low'; @endphp
                                        <tr>
                                            <td><b>{{ Str::limit($p->name, 35) }}</b><br><small
                                                    class="muted-sm">{{ optional($p->category)->name ?? '' }}</small>
                                            </td>
                                            <td><span class="stk {{ $cls }}">{{ $p->stock ?? 0 }}</span></td>
                                            <td><a class="btn-ghost-sm"
                                                    href="{{ route('admin.products.edit', $p->id) }}">Gérer</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="muted-sm">Aucune alerte stock.</p>
                    @endif
                </div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bolt" />
                        </svg> Meilleures ventes</h2>
                    @forelse($topVentes as $i => $p)
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
                    <div class="pdp-actions" style="margin-top:16px">
                        <a class="btn-line" href="{{ route('admin.products.index') }}"><svg class="ic">
                                <use href="#i-grid" />
                            </svg> Gérer les produits</a>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-user" />
                    </svg> Derniers clients</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Ville</th>
                                <th>Inscrit le</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($derniersClients as $u)
                                <tr>
                                    <td><b>{{ $u->name }}</b></td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->ville ?? '—' }}</td>
                                    <td>{{ $u->created_at?->format('d/m/Y') }}</td>
                                    <td><a class="btn-ghost-sm" href="{{ route('admin.users.show', $u->id) }}">Voir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted-sm">Aucun client.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
