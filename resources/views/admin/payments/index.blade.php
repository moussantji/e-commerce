@extends('admin.base')

@section('title', 'Modération des paiements')

@section('content')
<div class="content">
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Paiements à vérifier</h4>
            <p class="text-muted mb-0 small">Confirmez ou rejetez les paiements de commandes et les rechargements.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Commandes
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Compteurs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width:44px;height:44px;">
                        <i class="fas fa-shopping-bag"></i>
                    </span>
                    <div>
                        <div class="h4 mb-0">{{ $payments->count() }}</div>
                        <div class="text-muted small">Commandes en attente</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success" style="width:44px;height:44px;">
                        <i class="fas fa-wallet"></i>
                    </span>
                    <div>
                        <div class="h4 mb-0">{{ $topups->count() }}</div>
                        <div class="text-muted small">Rechargements en attente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Paiements de commandes --}}
    <h6 class="text-uppercase text-muted fw-bold small mb-3">Paiements de commandes</h6>
    <div class="row g-3 mb-4">
        @forelse ($payments as $p)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ optional($p->user)->name ?? 'Client' }}</div>
                                <a href="{{ route('commande.show', $p->order_id) }}" class="small text-decoration-none">
                                    {{ optional($p->order)->numero_commande ?? '#' . $p->order_id }}
                                </a>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase">{{ $p->provider }}</span>
                        </div>

                        <div class="h4 mb-3">{{ number_format($p->amount, 0, ',', ' ') }} <span class="fs-6 text-muted">FCFA</span></div>

                        <ul class="list-unstyled small text-muted mb-3">
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <span>Téléphone</span>
                                <span class="text-body">{{ $p->phone ?? '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-1">
                                <span>Référence</span>
                                <span class="text-body text-truncate ms-2" style="max-width:60%;">{{ $p->notes ?? '—' }}</span>
                            </li>
                        </ul>

                        <div class="mt-auto d-flex gap-2">
                            <form action="{{ route('admin.payments.confirm', $p->id) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <button class="btn btn-success w-100 btn-sm" onclick="return confirm('Confirmer ce paiement ?')">
                                    <i class="fas fa-check me-1"></i> Confirmer
                                </button>
                            </form>
                            <form action="{{ route('admin.payments.reject', $p->id) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm('Rejeter ce paiement ?')">
                                    <i class="fas fa-times me-1"></i> Rejeter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                        Aucun paiement de commande en attente.
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Rechargements de portefeuille --}}
    <h6 class="text-uppercase text-muted fw-bold small mb-3">Rechargements de portefeuille</h6>
    <div class="row g-3">
        @forelse ($topups as $t)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="fw-semibold text-truncate">{{ optional($t->user)->name ?? 'Client' }}</div>
                            <span class="badge bg-success bg-opacity-10 text-success text-uppercase">{{ $t->method }}</span>
                        </div>

                        <div class="h4 mb-3">{{ number_format($t->amount, 0, ',', ' ') }} <span class="fs-6 text-muted">FCFA</span></div>

                        <ul class="list-unstyled small text-muted mb-3">
                            <li class="d-flex justify-content-between border-bottom py-1">
                                <span>Téléphone</span>
                                <span class="text-body">{{ $t->phone ?? '—' }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-1">
                                <span>Référence</span>
                                <span class="text-body text-truncate ms-2" style="max-width:60%;">{{ $t->reference ?? '—' }}</span>
                            </li>
                        </ul>

                        <div class="mt-auto d-flex gap-2">
                            <form action="{{ route('admin.topups.confirm', $t->id) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <button class="btn btn-success w-100 btn-sm" onclick="return confirm('Confirmer et créditer ?')">
                                    <i class="fas fa-check me-1"></i> Confirmer
                                </button>
                            </form>
                            <form action="{{ route('admin.topups.reject', $t->id) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-danger w-100 btn-sm" onclick="return confirm('Rejeter ?')">
                                    <i class="fas fa-times me-1"></i> Rejeter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                        Aucun rechargement en attente.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
</div>
@endsection
