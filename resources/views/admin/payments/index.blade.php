@extends('admin.base')

@section('title', 'Modération des paiements')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Paiements</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Paiements à vérifier</h1>
            <p>Confirmez ou rejetez les paiements de commandes et les rechargements.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="kpis">
                <div class="kpi"><span class="kpi-l">Commandes en attente</span><b>{{ $payments->count() }}</b><span
                        class="kpi-n">preuves à vérifier</span></div>
                <div class="kpi"><span class="kpi-l">Rechargements en attente</span><b>{{ $topups->count() }}</b><span
                        class="kpi-n">portefeuilles à créditer</span></div>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-card" />
                    </svg> Paiements de commandes <span class="badge-nb">{{ $payments->count() }}</span></h2>
                @forelse ($payments as $p)
                    @php
                        $phs = $p->photos ?? [];
                        if (is_string($phs)) {
                            $phs = json_decode($phs, true) ?: [];
                        }
                    @endphp
                    <div class="ocmd">
                        <div class="ocmd-top">
                            <div><b>{{ optional($p->user)->name ?? 'Client' }}</b>
                                <a class="lien"
                                    href="{{ route('admin.orders.show', $p->order_id) }}">{{ optional($p->order)->numero_commande ?? '#' . $p->order_id }}</a>
                                <span class="muted-sm">{{ $p->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <span
                                style="font-size:11.5px;font-weight:700;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:4px 11px">{{ ucfirst($p->provider) }}</span>
                        </div>
                        <div class="ocmd-lignes">
                            Tél. {{ $p->phone ?? '—' }} · Réf. {{ $p->notes ?? '—' }}
                        </div>
                        @if (!empty($phs))
                            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
                                @foreach ($phs as $ph)
                                    <a href="{{ asset('storage/' . $ph) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $ph) }}" alt="Preuve de paiement"
                                            loading="lazy"
                                            style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1px solid var(--line)">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <div class="ocmd-foot">
                            <b>{{ number_format($p->amount, 0, ',', ' ') }} FCFA</b>
                            <span style="display:flex;gap:8px">
                                <form action="{{ route('admin.payments.confirm', $p->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('PATCH')
                                    <button class="btn-solid" style="font-size:12.5px;padding:9px 18px" type="submit"
                                        onclick="return confirm('Confirmer ce paiement ?')">Confirmer</button>
                                </form>
                                <form action="{{ route('admin.payments.reject', $p->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="reason" value="">
                                    <button class="btn-ghost-sm" type="submit" style="color:var(--pink)"
                                        onclick="return confirm('Rejeter ce paiement ?')">Rejeter</button>
                                </form>
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="muted-sm">Aucun paiement de commande en attente. 🎉</p>
                @endforelse
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bolt" />
                    </svg> Rechargements de portefeuille <span class="badge-nb">{{ $topups->count() }}</span></h2>
                @forelse ($topups as $t)
                    <div class="ocmd">
                        <div class="ocmd-top">
                            <div><b>{{ optional($t->user)->name ?? 'Client' }}</b>
                                <span class="muted-sm">{{ $t->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <span
                                style="font-size:11.5px;font-weight:700;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:4px 11px">{{ ucfirst($t->method) }}</span>
                        </div>
                        <div class="ocmd-foot">
                            <b>{{ number_format($t->amount, 0, ',', ' ') }} FCFA</b>
                            <span style="display:flex;gap:8px">
                                <form action="{{ route('admin.topups.confirm', $t->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('PATCH')
                                    <button class="btn-solid" style="font-size:12.5px;padding:9px 18px" type="submit"
                                        onclick="return confirm('Confirmer ce rechargement ?')">Confirmer</button>
                                </form>
                                <form action="{{ route('admin.topups.reject', $t->id) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('PATCH')
                                    <button class="btn-ghost-sm" type="submit" style="color:var(--pink)"
                                        onclick="return confirm('Rejeter ce rechargement ?')">Rejeter</button>
                                </form>
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="muted-sm">Aucun rechargement en attente.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
