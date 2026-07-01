@extends('admin.base')

@section('title', 'Modération des paiements')

@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-4">Paiements à vérifier</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Paiements de commandes --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Commandes ({{ $payments->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Commande</th>
                            <th>Méthode</th>
                            <th>Téléphone</th>
                            <th>Référence</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $p)
                            <tr>
                                <td>{{ optional($p->user)->name ?? 'Client' }}</td>
                                <td>
                                    <a href="{{ route('commande.show', $p->order_id) }}">
                                        {{ optional($p->order)->numero_commande ?? '#' . $p->order_id }}
                                    </a>
                                </td>
                                <td>{{ $p->provider }}</td>
                                <td>{{ $p->phone ?? '—' }}</td>
                                <td>{{ $p->notes ?? '—' }}</td>
                                <td class="text-end fw-bold">{{ number_format($p->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.payments.confirm', $p->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Confirmer ce paiement ?')">
                                            <i class="fas fa-check"></i> Confirmer
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.payments.reject', $p->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Rejeter ce paiement ?')">
                                            <i class="fas fa-times"></i> Rejeter
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucun paiement en attente.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Rechargements de portefeuille --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Rechargements de portefeuille ({{ $topups->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Méthode</th>
                            <th>Téléphone</th>
                            <th>Référence</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topups as $t)
                            <tr>
                                <td>{{ optional($t->user)->name ?? 'Client' }}</td>
                                <td>{{ $t->method }}</td>
                                <td>{{ $t->phone ?? '—' }}</td>
                                <td>{{ $t->reference ?? '—' }}</td>
                                <td class="text-end fw-bold">{{ number_format($t->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.topups.confirm', $t->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Confirmer et créditer ?')">
                                            <i class="fas fa-check"></i> Confirmer
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.topups.reject', $t->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Rejeter ?')">
                                            <i class="fas fa-times"></i> Rejeter
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucun rechargement en attente.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
