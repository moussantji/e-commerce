@extends('admin.base')

@section('title', 'Gestion des tags')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Tags</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Tags</h1>
            <p>Étiquettes pour retrouver les produits.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.tags.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Nouveau tag</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bolt" />
                    </svg> Liste des tags</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Produits</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                                <tr>
                                    <td><b>{{ $tag->name }}</b></td>
                                    <td><small class="muted-sm">{{ $tag->slug }}</small></td>
                                    <td>{{ $tag->description ?? '—' }}</td>
                                    <td><span class="badge-nb">{{ $tag->products_count ?? 0 }}</span></td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.tags.edit', $tag) }}">Modifier</a>
                                        <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Supprimer ce tag ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted-sm">Aucun tag.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
