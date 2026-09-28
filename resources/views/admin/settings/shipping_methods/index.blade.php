@extends('admin.base')

@section('title', 'Modes de livraison')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Livraison</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Modes de livraison</h1>
            <p>Tarifs, délais et zones de livraison.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.shipping-methods.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Ajouter un mode</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-truck" />
                    </svg> Liste des modes</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Délai</th>
                                <th>Prix</th>
                                <th>Franco dès</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippingMethods as $method)
                                <tr>
                                    <td><b>{{ $method->method_name }}</b></td>
                                    <td>
                                        @if ($method->delivery_time_min || $method->delivery_time_max)
                                            {{ $method->delivery_time_min ?? '—' }}–{{ $method->delivery_time_max ?? '—' }}
                                            {{ $method->delivery_time_unit }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td><b>{{ number_format($method->price, 0, ',', ' ') }} FCFA</b></td>
                                    <td>{{ $method->free_shipping_threshold ? number_format($method->free_shipping_threshold, 0, ',', ' ') . ' FCFA' : '—' }}
                                    </td>
                                    <td>{!! $method->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.shipping-methods.edit', $method->id) }}">Modifier</a>
                                        <form action="{{ route('admin.shipping-methods.destroy', $method->id) }}"
                                            method="POST" style="display:inline"
                                            onsubmit="return confirm('Supprimer ce mode ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted-sm">Aucun mode.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
