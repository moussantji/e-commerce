@extends('admin.base')

@section('title', 'Liste des utilisateurs')

@section('content')
    @php
        $filters = [
            null => 'Tous',
            'active' => 'Actifs',
            'banned' => 'Bannis',
            'admins' => 'Admins',
            'new' => 'Nouveaux',
        ];
    @endphp
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Clients</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Clients & utilisateurs</h1>
            <p>{{ $counts['all'] ?? $users->count() }} compte{{ ($counts['all'] ?? $users->count()) > 1 ? 's' : '' }} au
                total.</p>
            <div class="cats" style="padding:14px 0 0">
                @foreach ($filters as $key => $label)
                    <a class="cat {{ request('filter') === $key || (is_null($key) && !request('filter')) ? 'hot' : '' }}"
                        href="{{ $key ? route('admin.users.index', ['filter' => $key]) : route('admin.users.index') }}">{{ $label }}
                        ({{ $counts[$key ?? 'all'] ?? 0 }})</a>
                @endforeach
            </div>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="toolbar">
                <form action="{{ route('admin.users.index') }}" method="GET"
                    style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1">
                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <input class="ctrl" style="border-radius:12px;min-width:220px" type="search" name="search"
                        placeholder="Nom, email..." value="{{ request('search') }}" aria-label="Rechercher">
                    @if (request('search'))
                        <a class="btn-ghost-sm"
                            href="{{ route('admin.users.index', request('filter') ? ['filter' => request('filter')] : []) }}">Effacer</a>
                    @endif
                </form>
                <span class="grow"></span>
                <a class="btn-solid" style="font-size:13.5px;padding:11px 22px"
                    href="{{ route('admin.users.create') }}"><svg class="ic" style="width:16px;height:16px">
                        <use href="#i-b2-plus" />
                    </svg> Nouvel utilisateur</a>
            </div>

            <div class="panel">
                <h2><svg class="ic">
                        <use href="#i-user" />
                    </svg> Liste des utilisateurs</h2>
                <div class="table-scroll">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Commandes</th>
                                <th>Dépensé</th>
                                <th>Ville</th>
                                <th>Dernière connexion</th>
                                <th>Statut</th>
                                <th>Rôle</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $uphoto = method_exists($user, 'getPhoto') && $user->getPhoto() ? $user->getPhoto()->getImageUrl(100, 100) : null;
                                    $uinit = mb_strtoupper(mb_substr($user->name ?? '?', 0, 1));
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            style="display:flex;align-items:center;gap:10px">
                                            @if ($uphoto)
                                                <img src="{{ $uphoto }}" alt="{{ $user->name }}"
                                                    style="width:38px;height:38px;border-radius:50%;object-fit:cover;flex:none">
                                            @else
                                                <span
                                                    style="width:38px;height:38px;border-radius:50%;display:grid;place-items:center;color:#fff;font-weight:800;font-size:15px;background:linear-gradient(135deg,var(--violet-600),var(--violet-400));flex:none">{{ $uinit }}</span>
                                            @endif
                                            <b>{{ $user->name }}</b>
                                        </a>
                                    </td>
                                    <td><a class="lien" href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                    <td><b>{{ $user->orders_count ?? 0 }}</b></td>
                                    <td><b>{{ isset($user->total_spent) && $user->total_spent ? number_format($user->total_spent, 0, ',', ' ') . ' FCFA' : '0 FCFA' }}</b>
                                    </td>
                                    <td>{{ $user->ville ?? 'N/A' }}</td>
                                    <td>{{ $user->last_login ? $user->last_login->diffForHumans() : 'Jamais' }}</td>
                                    <td>{!! ($user->status ?? '') === 'active' ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}</td>
                                    <td>
                                        <form action="{{ route('admin.users.role', $user) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <select class="ctrl ctrl-sm" name="role" onchange="this.form.submit()"
                                                aria-label="Rôle">
                                                <option value="customer"
                                                    {{ $user->role === 'customer' ? 'selected' : '' }}>Client</option>
                                                <option value="admin"
                                                    {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><a class="btn-ghost-sm"
                                            href="{{ route('admin.users.show', $user) }}">Voir</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="muted-sm">Aucun utilisateur.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
