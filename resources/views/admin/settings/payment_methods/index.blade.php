@extends('admin.base')

@section('title', 'Moyens de paiement')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Moyens de paiement</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Moyens de paiement</h1>
            <p>Orange Money, Wave, espèces à la livraison...</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.payment-methods.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Ajouter une méthode</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-card" />
                    </svg> Liste des méthodes</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Frais</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentMethods as $method)
                                <tr>
                                    <td>
                                        @if ($method->getPhoto())
                                            <img src="{{ $method->getPhoto()->getImageUrl(80, 80) }}"
                                                alt="{{ $method->method_name }}"
                                                style="width:40px;height:40px;object-fit:cover;border-radius:12px;border:1px solid var(--line)">
                                        @else
                                            <span
                                                style="display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:var(--lav-1);color:var(--violet-400)"><svg
                                                    class="ic">
                                                    <use href="#i-card" />
                                                </svg></span>
                                        @endif
                                    </td>
                                    <td><b>{{ $method->method_name }}</b></td>
                                    <td>{{ Str::limit($method->description, 50) ?: '—' }}</td>
                                    <td>
                                        @if ($method->fee_percentage > 0)
                                            {{ $method->fee_percentage }}%
                                            @if ($method->fee > 0)
                                                + {{ number_format($method->fee, 0, ',', ' ') }} FCFA
                                            @endif
                                        @elseif($method->fee > 0)
                                            {{ number_format($method->fee, 0, ',', ' ') }} FCFA
                                        @else
                                            Gratuit
                                        @endif
                                    </td>
                                    <td>{!! $method->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.payment-methods.edit', $method->id) }}">Modifier</a>
                                        <form action="{{ route('admin.payment-methods.destroy', $method->id) }}"
                                            method="POST" style="display:inline"
                                            onsubmit="return confirm('Supprimer cette méthode ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted-sm">Aucune méthode.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
