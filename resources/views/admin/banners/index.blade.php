@extends('admin.base')

@section('title', 'Gestion des bannières')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Bannières</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Bannières</h1>
            <p>Visuels promotionnels de la boutique.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.banners.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Nouvelle bannière</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-bolt" />
                    </svg> Liste des bannières</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Titre principal</th>
                                <th>Titre secondaire</th>
                                <th>Promo</th>
                                <th>Bouton</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                                <tr>
                                    <td>
                                        @if ($banner->getPhoto())
                                            <img src="{{ $banner->getPhoto()->getImageUrl(530, 530) }}"
                                                alt="{{ $banner->title1_short }}"
                                                style="width:96px;height:48px;object-fit:cover;border-radius:10px;border:1px solid var(--line)">
                                        @else
                                            <span class="muted-sm">—</span>
                                        @endif
                                    </td>
                                    <td><b>{{ $banner->title1_short }}</b></td>
                                    <td>{{ $banner->title2_short }}</td>
                                    <td>
                                        @if ($banner->percentage)
                                            <span class="off" style="position:static">-{{ $banner->percentage }}%</span>
                                        @else
                                            <span class="muted-sm">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($banner->button_link)
                                            <a class="lien" href="{{ $banner->button_link }}"
                                                target="_blank">Voir</a>
                                        @else
                                            <span class="muted-sm">Sans bouton</span>
                                        @endif
                                    </td>
                                    <td>{!! $banner->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td style="white-space:nowrap">
                                        <a class="btn-ghost-sm"
                                            href="{{ route('admin.banners.edit', $banner) }}">Modifier</a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Supprimer cette bannière ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost-sm" style="color:var(--pink)">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted-sm">Aucune bannière.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
