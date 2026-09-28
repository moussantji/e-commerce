@extends('admin.base')

@section('title', 'Gestion des coupons')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Coupons</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Coupons</h1>
            <p>Codes promo pour le panier.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.coupons.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Nouveau coupon</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-b2-tag" />
                    </svg> Liste des coupons</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Valeur</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Utilisations</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td><b>{{ $coupon->code }}</b></td>
                                    <td>{{ $coupon->type === 'percentage' ? 'Pourcentage' : 'Montant fixe' }}</td>
                                    <td><b>{{ $coupon->type === 'percentage' ? $coupon->value . ' %' : number_format($coupon->value, 0, ',', ' ') . ' FCFA' }}</b>
                                    </td>
                                    <td>{{ $coupon->starts_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td>{{ $coupon->expires_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td><span class="badge-nb">{{ $coupon->usage_count }} /
                                            {{ $coupon->usage_limit ?? '∞' }}</span></td>
                                    <td>{!! $coupon->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.coupons.edit', $coupon) }}">Modifier</a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Supprimer ce coupon ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="muted-sm">Aucun coupon.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
