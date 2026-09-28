@extends('admin.base')

@section('title', 'Liste des caractéristiques')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Caractéristiques</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Caractéristiques</h1>
            <p>Types d'options proposées sur les fiches produits (stockage, taille, couleur...).</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.caracteristiques.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Ajouter une caractéristique</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-grid" />
                    </svg> Liste des caractéristiques</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Unité</th>
                                <th>Filtrable</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($caracteristiques as $caracteristique)
                                <tr>
                                    <td><b>{{ $caracteristique->name }}</b></td>
                                    <td><small
                                            style="font-size:11.5px;font-weight:700;background:var(--lav-1);border:1px solid #ddd6fe;color:var(--violet-800);border-radius:999px;padding:3px 10px">{{ $caracteristique->type ?: '—' }}</small>
                                    </td>
                                    <td>{{ $caracteristique->unite ?: '—' }}</td>
                                    <td>{!! $caracteristique->is_filterable ? '<span class="st ok">Oui</span>' : '<span class="st ko">Non</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.caracteristiques.edit', $caracteristique->id) }}">Modifier</a>
                                        <form
                                            action="{{ route('admin.caracteristiques.destroy', $caracteristique->id) }}"
                                            method="POST" style="display:inline"
                                            onsubmit="return confirm('Supprimer cette caractéristique ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted-sm">Aucune caractéristique.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
