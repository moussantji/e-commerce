@extends('admin.base')

@section('title', 'Gestion des catégories')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Catégories</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Catégories</h1>
            <p>Rayons et sous-rayons du catalogue.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.categories.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Nouvelle catégorie</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-grid" />
                    </svg> Liste des catégories</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nom</th>
                                <th>Hiérarchie</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        @if ($category->getPhoto())
                                            <img src="{{ $category->getPhoto()->getImageUrl(100, 100) }}"
                                                alt="{{ $category->name }}"
                                                style="width:48px;height:48px;object-fit:cover;border-radius:12px;border:1px solid var(--line)">
                                        @else
                                            <span
                                                style="display:grid;place-items:center;width:48px;height:48px;border-radius:12px;background:var(--lav-1);color:var(--violet-400)"><svg
                                                    class="ic">
                                                    <use href="#i-grid" />
                                                </svg></span>
                                        @endif
                                    </td>
                                    <td><b>{{ $category->name }}</b></td>
                                    <td>
                                        @if ($category->parent)
                                            <small class="muted-sm">Sous-catégorie de
                                                <b>{{ $category->parent->name }}</b></small>
                                        @else
                                            <span
                                                style="font-size:11.5px;font-weight:700;color:var(--violet-700)">Principale</span>
                                            @if ($category->children->count())
                                                <small class="muted-sm">·
                                                    {{ $category->children->count() }}
                                                    sous-catégorie{{ $category->children->count() > 1 ? 's' : '' }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{!! $category->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.categories.edit', $category) }}">Modifier</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)"
                                                onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted-sm">Aucune catégorie.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
