@extends('base')

@section('title', 'Mon espace client')

@section('content')
    @include('section-begin')

    @php
        $user = auth()->user();
        $cmds = $commandes ?? collect();
        $total = $cmds->sum('total');
        $enCours = $cmds->filter(fn($c) => \App\Support\OrderStatus::normalize($c->statut) !== \App\Support\OrderStatus::LIVRE)->count();
        $points = min(100, $cmds->count() * 15);
        $initiales = mb_strtoupper(mb_substr($user->prenom ?? $user->name ?? '?', 0, 1) . mb_substr(explode(' ', $user->name ?? '')[1] ?? '', 0, 1));
        $stClass = [
            'en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep',
            'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko',
        ];
    @endphp

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Mon espace client</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mon espace client</h1>
            <p>Vos commandes, leur suivi, vos favoris et vos informations de livraison.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-head">
                <div class="dash-who">
                    <span class="av-lg">{{ $initiales }}</span>
                    <div>
                        <h1>Bonjour {{ $user->prenom ?? $user->name }}</h1>
                        <p>{{ $user->email }} · client depuis {{ $user->created_at?->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="pdp-actions" style="margin:0">
                    <a class="btn-line" href="{{ route('products') }}"><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Commander</a>
                    <a class="btn-line" href="{{ route('profile.edit') }}"><svg class="ic">
                            <use href="#i-user" />
                        </svg> Mon profil</a>
                    @if (($user->role ?? '') === 'vendeur')
                        <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                            href="{{ route('vendeur.dashboard') }}"><svg class="ic" style="width:16px;height:16px">
                                <use href="#i-store" />
                            </svg> Espace vendeur</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button class="btn-line" type="submit"><svg class="ic">
                                <use href="#i-close" />
                            </svg> Se déconnecter</button>
                    </form>
                </div>
            </div>

            <div class="kpis">
                <div class="kpi"><span class="kpi-l">Commandes</span><b>{{ $cmds->count() }}</b><span
                        class="kpi-n">passées avec ce compte</span></div>
                <div class="kpi"><span class="kpi-l">En cours</span><b>{{ $enCours }}</b><span class="kpi-n">en
                        préparation ou expédiées</span></div>
                <div class="kpi"><span class="kpi-l">Total dépensé</span><b>{{ number_format($total, 0, ',', ' ') }}
                        FCFA</b><span class="kpi-n">livraison comprise</span></div>
                <div class="kpi"><span class="kpi-l">Fidélité</span><b>{{ $points }} / 100</b><span
                        class="kpi-n">points cumulés</span></div>
            </div>

            <div class="dash-grid">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Mes commandes</h2>
                    @forelse($cmds as $c)
                        @php $norm = \App\Support\OrderStatus::normalize($c->statut); @endphp
                        <div class="ocmd">
                            <div class="ocmd-top">
                                <div><b>{{ $c->numero_commande ?? 'CMD-' . $c->id }}</b>
                                    <span
                                        class="muted-sm">{{ $c->date_commande?->format('d/m/Y') ?? $c->created_at?->format('d/m/Y') }}</span>
                                </div>
                                <span class="st {{ $stClass[$norm] ?? 'conf' }}">{{ $c->status_label }}</span>
                            </div>
                            <div class="ocmd-lignes">
                                {{ $c->produits->map(fn($p) => Str::limit($p->name, 40) . ' ×' . ($p->pivot->quantite ?? 1))->join(' · ') }}
                            </div>
                            <div class="ocmd-foot">
                                <span>{{ $c->adresse_livraison['ville'] ?? '' }}</span>
                                <b>{{ number_format($c->total, 0, ',', ' ') }} FCFA</b>
                            </div>
                            <div class="pdp-actions" style="margin-top:10px">
                                @if ($norm === 'en_attente')
                                    <a class="btn-solid"
                                        style="font-size:13px;padding:11px 22px"
                                        href="{{ route('commande.show', $c->id) }}"><svg class="ic"
                                            style="width:16px;height:16px">
                                            <use href="#i-card" />
                                        </svg> Payer ·
                                        {{ number_format($c->total, 0, ',', ' ') }} FCFA</a>
                                @endif
                                <a class="btn-ghost-sm"
                                    href="{{ route('commande.show', $c->id) }}"><svg class="ic"
                                        style="width:15px;height:15px">
                                        <use href="#i-b2-info" />
                                    </svg> Suivre cette commande</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty" style="border:0;margin:0"><svg class="ic">
                                <use href="#i-bag" />
                            </svg>
                            <h3>Pas encore de commande</h3>
                            <p>Vos commandes apparaîtront ici avec leur suivi.</p>
                            <div class="pdp-actions" style="justify-content:center;margin-top:14px">
                                <a class="btn-solid" href="{{ route('products') }}">Commander</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-heart" />
                        </svg> Mes favoris <span class="badge-nb">{{ ($wishlist ?? collect())->count() }}</span></h2>
                    @forelse($wishlist ?? [] as $p)
                        @php $img = $p->getPhoto() ? $p->getPhoto()->getImageUrl(120, 120) : asset('assets/img/products/1.png'); @endphp
                        <div class="dligne">
                            <a class="th" style="background-image:url('{{ $img }}')"
                                href="{{ route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]) }}"></a>
                            <div><b><a
                                        href="{{ route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]) }}">{{ Str::limit($p->name, 45) }}</a></b>
                                <small>{{ number_format($p->sale_price ?? $p->price, 0, ',', ' ') }} FCFA</small></div>
                            <a class="btn-ghost-sm"
                                href="{{ route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]) }}">Voir</a>
                        </div>
                    @empty
                        <p class="muted-sm">Aucun favori. Cliquez sur le cœur d'un produit pour l'enregistrer.</p>
                    @endforelse
                </div>
            </div>

            <div class="dash-grid">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-user" />
                        </svg> Mes informations</h2>
                    <table class="spec">
                        <tbody>
                            <tr>
                                <td>Nom</td>
                                <td>{{ $user->prenom ?? $user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>Téléphone</td>
                                <td>{{ $user->tel ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td>Adresse</td>
                                <td>{{ $user->adresse ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-headset" />
                        </svg> Besoin d'aide ?</h2>
                    <p style="font-size:13.5px;color:#374151;line-height:1.75">Notre service client répond en moins
                        d'une heure du lundi au samedi, de 8 h à 20 h.</p>
                    <div class="pdp-actions" style="margin-top:14px">
                        <a class="btn-solid" href="{{ route('products') }}"><svg class="ic">
                                <use href="#i-headset" />
                            </svg> Voir le catalogue</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
