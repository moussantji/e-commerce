@extends('admin.base')

@section('title', 'Liste des marques')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Marques</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Marques</h1>
            <p>{{ $brands->count() }} marque{{ $brands->count() > 1 ? 's' : '' }} au catalogue.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.brands.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Ajouter une marque</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-b2-tag" />
                    </svg> Liste des marques</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Site web</th>
                                <th>Ordre</th>
                                <th>Produits</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                                <tr>
                                    <td>
                                        @if ($brand->getPhoto())
                                            <img src="{{ $brand->getPhoto()->getImageUrl(80, 80) }}"
                                                alt="{{ $brand->name }}"
                                                style="width:40px;height:40px;object-fit:cover;border-radius:12px;border:1px solid var(--line)">
                                        @else
                                            <span
                                                style="display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:var(--lav-1);color:var(--violet-400)"><svg
                                                    class="ic">
                                                    <use href="#i-b2-tag" />
                                                </svg></span>
                                        @endif
                                    </td>
                                    <td><b>{{ $brand->name }}</b></td>
                                    <td>{{ Str::limit($brand->description, 50) ?: '—' }}</td>
                                    <td>
                                        @if ($brand->website)
                                            <a class="lien" href="{{ $brand->website }}"
                                                target="_blank">{{ Str::limit($brand->website, 30) }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $brand->sort_order }}</td>
                                    <td><span class="badge-nb">{{ $brand->products()->count() }}</span></td>
                                    <td>{!! $brand->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.brands.edit', $brand) }}">Modifier</a>
                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                                            style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)"
                                                onclick="return confirm('Supprimer cette marque ?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="muted-sm">Aucune marque.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
