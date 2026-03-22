@extends('admin.base')

@section('title', 'Listes des utilisateurs')

@section('content')
    <div class="content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <!-- Breadcrumb -->
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Utilisateurs</li>
            </ol>
        </nav>

        <!-- Header + Navigation Tabs -->
        <div class="mb-4">
            <div class="row g-2 mb-4">
                <div class="col-auto">
                    <h2 class="mb-0">Utilisateurs</h2>
                </div>
            </div>
            <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === null ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <span>Tous</span>
                        <span class="text-body-tertiary fw-semibold">({{ $counts['all'] }})</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === 'active' ? 'active' : '' }}"
                        href="{{ route('admin.users.index', ['filter' => 'active']) }}">
                        <span>Actifs</span>
                        <span class="text-body-tertiary fw-semibold">({{ $counts['active'] }})</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === 'banned' ? 'active' : '' }}"
                        href="{{ route('admin.users.index', ['filter' => 'banned']) }}">
                        <span>Bannis</span>
                        <span class="text-body-tertiary fw-semibold">({{ $counts['banned'] }})</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === 'admins' ? 'active' : '' }}"
                        href="{{ route('admin.users.index', ['filter' => 'admins']) }}">
                        <span>Admins</span>
                        <span class="text-body-tertiary fw-semibold">({{ $counts['admins'] }})</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') === 'new' ? 'active' : '' }}"
                        href="{{ route('admin.users.index', ['filter' => 'new']) }}">
                        <span>Nouveaux</span>
                        <span class="text-body-tertiary fw-semibold">
                            (+{{ $counts['new'] }})
                        </span>
                    </a>
                </li>
            </ul>


            <!-- Barre de recherche + Filtres + Actions -->
            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-auto">
                        <div class="search-box">
                            <form class="position-relative" onsubmit="return false;">
                                <input id="user-search" class="form-control search-input search" type="search"
                                    placeholder="Rechercher un utilisateur..." aria-label="Search" data-list-search />

                                <span class="fas fa-search search-box-icon"></span>
                            </form>
                        </div>

                    </div>
                    <div class="col-auto">
                        <button id="user-reset" class="btn btn-link text-body me-3 px-0">
                            <span class="fas fa-sync-alt fs-9 me-1"></span>Réinitialiser
                        </button>

                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <span class="fas fa-plus me-2"></span>Nouvel utilisateur
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tableau utilisateurs List.js -->
            <div id="users" data-list='{"valueNames":["name","email"],"page":15,"pagination":true}'>
                <div
                    class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                    <div class="table-responsive scrollbar-overlay mx-n1 px-1">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr>
                                    <th class="white-space-nowrap fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" id="checkbox-bulk-users-select" type="checkbox"
                                                data-bulk-select='{"body":"users-table-body"}' />
                                        </div>
                                    </th>
                                    <th class="sort align-middle pe-3" scope="col" data-sort="name" style="width:18%;">
                                        UTILISATEUR</th>
                                    <th class="sort align-middle pe-3" scope="col" data-sort="email" style="width:22%;">
                                        EMAIL</th>
                                    <th class="sort align-middle text-end pe-3" scope="col" data-sort="orders"
                                        style="width:8%;">COMMANDES</th>
                                    <th class="sort align-middle text-end pe-3" scope="col" data-sort="spent"
                                        style="width:12%;">DÉPENSES</th>
                                    <th class="sort align-middle pe-3" scope="col" data-sort="city" style="width:15%;">
                                        VILLE</th>
                                    <th class="sort align-middle text-end pe-2" scope="col" data-sort="last_login"
                                        style="width:12%;">DERNIÈRE CONNEXION</th>
                                    <th class="sort align-middle pe-2" scope="col" data-sort="status" style="width:8%;">
                                        STATUT</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="users-table-body">
                                @forelse($users as $user)
                                    <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                        <td class="fs-9 align-middle ps-0 py-3">
                                            <div class="form-check mb-0 fs-8">
                                                <input class="form-check-input" type="checkbox" data-bulk-select-row />
                                            </div>
                                        </td>
                                        <td class="name align-middle white-space-nowrap pe-3">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="d-flex align-items-center text-body-emphasis">
                                                <div class="avatar avatar-m">
                                                    @if ($user->avatar)
                                                        <img class="rounded-circle"
                                                            src="{{ asset('storage/' . $user->avatar) }}"
                                                            alt="{{ $user->name }}" />
                                                    @else
                                                        <div class="avatar-name rounded-circle bg-primary">
                                                            <span
                                                                class="fs-9 fw-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="mb-0 ms-3 text-body-emphasis fw-bold">{{ $user->name }}</p>
                                            </a>
                                        </td>
                                        <td class="email align-middle white-space-nowrap pe-3">
                                            <a class="fw-semibold text-body-highlight" href="mailto:{{ $user->email }}">
                                                {{ $user->email }}
                                            </a>
                                        </td>
                                        <td class="orders align-middle text-end pe-3 fw-semibold text-body-highlight">
                                            {{ $user->orders_count ?? 0 }}
                                        </td>
                                        <td class="spent align-middle text-end pe-3 fw-bold text-primary">
                                            {{ $user->total_spent ? number_format($user->total_spent, 0) . ' FCFA' : '0 FCFA' }}
                                        </td>
                                        <td class="city align-middle pe-3 text-body-highlight">
                                            {{ $user->city ?? 'N/A' }}
                                        </td>
                                        <td class="last_login align-middle text-end pe-2 text-body-tertiary">
                                            {{ $user->last_login ? $user->last_login->diffForHumans() : 'Jamais' }}
                                        </td>
                                        <td class="status align-middle pe-3">
                                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3 opacity-75"></i>
                                                <p>Aucun utilisateur trouvé</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination List.js -->
                    <div class="row align-items-center justify-content-between py-3 pe-0 fs-9">
                        <div class="col-auto d-flex">
                            <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info">
                            </p>
                            <a class="fw-semibold d-none" href="#" data-list-view="*">Voir tout</a>
                            <a class="fw-semibold d-block" href="#" data-list-view="less">Voir moins</a>
                        </div>
                        <div class="col-auto d-flex">
                            <button class="page-link me-2" data-list-pagination="prev">
                                <span class="fas fa-chevron-left"></span>
                            </button>
                            <ul class="mb-0 pagination"></ul>
                            <button class="page-link ms-2" data-list-pagination="next">
                                <span class="fas fa-chevron-right"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- List.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/list.js@2.3.1"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('users');
            if (!container) {
                console.error('Container #users introuvable');
                return;
            }

            // Vérifie que List.js est bien dispo
            if (typeof List === 'undefined') {
                console.error('List.js non chargé (List est undefined)');
                return;
            }

            const userList = new List(container, {
                valueNames: ['name', 'email']
            });

            // Petit log pour vérifier que la recherche est bien connectée
            const input = document.getElementById('user-search');
            const reset = document.getElementById('user-reset');
            if (input) {
                input.addEventListener('input', function() {
                    const q = this.value;
                    userList.search(q); // <<< c'est ça qui applique le filtre
                });
            }
            if (reset) {
                reset.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (input) input.value = ''; // vider le champ
                    userList.search(''); // enlever le filtre
                });
            }
        });
    </script>
@endsection
