@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Utilisateur #{{ $user->id }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        {{-- Fiche identité --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Profil</h5>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Nom</dt>
                        <dd class="col-7">{{ $user->name }}</dd>

                        <dt class="col-5 text-muted">Email</dt>
                        <dd class="col-7">{{ $user->email }}</dd>

                        <dt class="col-5 text-muted">Téléphone</dt>
                        <dd class="col-7">{{ $user->tel ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">Statut</dt>
                        <dd class="col-7">
                            <span class="badge {{ ($user->status ?? '') === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ($user->status ?? '') === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </dd>

                        <dt class="col-5 text-muted">Inscrit le</dt>
                        <dd class="col-7">{{ $user->created_at?->format('d/m/Y') ?? 'N/A' }}</dd>
                    </dl>
                </div>

                {{-- Changement rapide de rôle --}}
                <div class="card-footer">
                    <form action="{{ route('admin.users.role', $user) }}" method="POST"
                        class="d-flex align-items-center gap-2">
                        @csrf @method('PATCH')
                        <label class="form-label mb-0 small text-muted">Rôle :</label>
                        <select name="role" class="form-select form-select-sm" style="width:auto;"
                            onchange="this.form.submit()">
                            <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Client</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- Historique des commandes --}}
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Commandes ({{ $user->orders->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->orders->sortByDesc('created_at') as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ $order->status_label ?? $order->statut }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($order->total ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                                class="btn btn-sm btn-outline-primary">Voir</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Aucune commande
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
