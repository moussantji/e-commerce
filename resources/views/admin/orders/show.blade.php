@extends('admin.base')

@section('title', 'Commande ' . ($order->numero_commande ?? '#' . $order->id))

@section('content')
    @php
        use App\Support\OrderStatus;
        $st = OrderStatus::normalize($order->statut);
        $terminal = OrderStatus::isTerminal($order->statut);
        $stClass = ['en_attente' => 'conf', 'paiement_declare' => 'conf', 'payee' => 'prep', 'expedie' => 'exp', 'livre' => 'ok', 'annule' => 'ko'];
        $liv = $order->adresse_livraison;
        $fac = $order->adresse_facturation;
    @endphp

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.orders.index') }}">Commandes</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ $order->numero_commande ?? 'CMD-' . $order->id }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Commande {{ $order->numero_commande ?? '#' . $order->id }}</h1>
            <p>{{ $order->created_at?->format('d/m/Y H:i') }} · {{ optional($order->user)->name ?? 'Client inconnu' }} ·
                <b style="color:var(--pink)">{{ number_format($order->total, 0, ',', ' ') }} FCFA</b>
            </p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <div class="ocmd-top" style="margin-bottom:6px">
                    <div>
                        <h2 style="margin:0"><svg class="ic">
                                <use href="#i-bag" />
                            </svg> {{ $order->numero_commande ?? 'CMD-' . $order->id }}</h2>
                        <span class="muted-sm">{{ optional($order->user)->email ?? '' }}
                            {{ optional($order->user)->tel ? '· ' . optional($order->user)->tel : '' }}</span>
                    </div>
                    <span class="st {{ $stClass[$st] ?? 'conf' }}">{{ $order->status_label }}</span>
                </div>

                @unless ($terminal)
                    <div class="tagline-band">
                        <svg class="ic">
                            <use href="#i-bolt" />
                        </svg>
                        <span>Faites avancer la commande : le client est notifié à chaque changement.</span>
                    </div>
                    <div class="pdp-actions" style="margin-top:12px">
                        @if (in_array($st, ['en_attente', 'paiement_declare']))
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                                style="display:inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="payee">
                                <button class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                                    onclick="return confirm('Confirmer le paiement ?')"><svg class="ic"
                                        style="width:16px;height:16px">
                                        <use href="#i-b2-check" />
                                    </svg> Confirmer le paiement</button>
                            </form>
                        @endif
                        @if (in_array($st, ['payee']))
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                                style="display:inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="expedie">
                                <button class="btn-line" type="submit"><svg class="ic">
                                        <use href="#i-truck" />
                                    </svg> Marquer expédiée</button>
                            </form>
                        @endif
                        @if (in_array($st, ['expedie']))
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                                style="display:inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="livre">
                                <button class="btn-line" type="submit"><svg class="ic">
                                        <use href="#i-b2-check" />
                                    </svg> Marquer livrée</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                            style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="annule">
                            <button class="btn-ghost-sm" type="submit"
                                onclick="return confirm('Annuler cette commande ?')">Annuler</button>
                        </form>
                    </div>
                @endunless
            </div>

            <div class="dash-grid">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-grid" />
                        </svg> Produits commandés</h2>
                    <div class="table-scroll">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unit.</th>
                                    <th>Qté</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->produits as $produit)
                                    @php
                                        $pOpts = $produit->pivot->options ?? null;
                                        if (is_string($pOpts)) {
                                            $pOpts = json_decode($pOpts, true);
                                        }
                                    @endphp
                                    <tr>
                                        <td><b>{{ $produit->name }}</b><br><small class="muted-sm">Réf :
                                                {{ $produit->sku ?? ('REF-' . $produit->id) }}</small>
                                            @if (!empty($pOpts) && is_array($pOpts))
                                                <br>
                                                @foreach ($pOpts as $ok => $ov)
                                                    <small
                                                        style="display:inline-block;font-size:11px;font-weight:600;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:2px 9px;margin:3px 4px 0 0">{{ $ok }}
                                                        : {{ $ov }}</small>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ number_format($produit->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                        <td>×{{ $produit->pivot->quantite }}</td>
                                        <td><b>{{ number_format($produit->pivot->total, 0, ',', ' ') }} FCFA</b></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="srow"><span>Sous-total</span><b>{{ number_format($order->produits->sum('pivot.total'), 0, ',', ' ') }}
                            FCFA</b></div>
                    @if ($order->frais_livraison > 0)
                        <div class="srow"><span>Livraison</span><b>{{ number_format($order->frais_livraison, 0, ',', ' ') }}
                                FCFA</b></div>
                    @endif
                    @if (($order->remise ?? 0) > 0)
                        <div class="srow ok"><span>Remise</span><b>−{{ number_format($order->remise, 0, ',', ' ') }}
                                FCFA</b></div>
                    @endif
                    <div class="stotal"><span>Total</span><b>{{ number_format($order->total, 0, ',', ' ') }} FCFA</b>
                    </div>
                </div>
                <div>
                    <div class="panel">
                        <h2><svg class="ic">
                                <use href="#i-truck" />
                            </svg> Livraison</h2>
                        <table class="spec">
                            <tbody>
                                <tr>
                                    <td>Client</td>
                                    <td><b>{{ $order->user->name ?? '—' }}</b><br><small
                                            class="muted-sm">{{ $order->user->email ?? '' }}</small></td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td>
                                        @if (is_array($liv) && !empty($liv))
                                            {{ data_get($liv, 'adresse') }}<br>{{ data_get($liv, 'ville') }}@if (data_get($liv, 'pays'))
                                                , {{ data_get($liv, 'pays') }}
                                            @endif
                                        @else
                                            {{ is_string($liv) ? $liv : '—' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mode</td>
                                    <td>{{ optional($order->livraison)->method_name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td>Paiement</td>
                                    <td>{{ optional($order->paiement)->method_name ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel">
                        <h2><svg class="ic">
                                <use href="#i-card" />
                            </svg> Preuve de paiement</h2>
                        @php $preuves = \App\Models\PaymentProof::where('order_id', $order->id)->latest()->get(); @endphp
                        @forelse($preuves as $pv)
                            <div class="dligne">
                                <div><b>{{ ucfirst($pv->provider) }}</b>
                                    <small>{{ $pv->phone ?? '' }} ·
                                        {{ number_format($pv->amount, 0, ',', ' ') }} FCFA ·
                                        {{ $pv->created_at?->format('d/m/Y H:i') }}</small>
                                </div>
                                <span class="st {{ $pv->status === 'confirme' ? 'ok' : ($pv->status === 'rejete' ? 'ko' : 'conf') }}">{{ $pv->status }}</span>
                            </div>
                        @empty
                            <p class="muted-sm">Aucune preuve envoyée.</p>
                        @endforelse
                        <div class="pdp-actions" style="margin-top:14px">
                            <a class="btn-line" href="{{ route('admin.payments.moderation') }}">Modérer les
                                paiements</a>
                            <a class="btn-ghost-sm" href="{{ route('admin.orders.index') }}">Retour à la liste</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
