@extends('admin.base')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Méthodes de Paiement @if (session()->has('error')) {{ ' - ' }} {{ session('error') }}  @endif @if (session()->has('success')) {{ ' - ' }} {{ session('success') }}  @endif</h4>
            <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une méthode
            </a>
        </div>
        <div class="card-body">
            <!-- Desktop / large screens: table view -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Frais</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $method)
                        <tr>
                            <td>{{ $method->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($method->getPhoto())
                                        <img src="{{ $method->getPhoto()->getImageUrl(80,80) }}" alt="{{ $method->method_name }}" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                    @endif
                                    {{ $method->method_name }}
                                </div>
                            </td>
                            <td>{{ Str::limit($method->description, 50) }}</td>
                            <td>
                                @if($method->fee_percentage > 0)
                                    {{ $method->fee_percentage }}%
                                    @if($method->fee > 0)
                                        + {{ number_format($method->fee, 2) }} FCFA
                                    @endif
                                @elseif($method->fee > 0)
                                    {{ number_format($method->fee, 2) }} FCFA
                                @else
                                    Gratuit
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $method->is_active ? 'success' : 'danger' }}">
                                    {{ $method->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePaymentMethod{{ $method->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deletePaymentMethod{{ $method->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer la méthode de paiement "{{ $method->method_name }}" ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('admin.payment-methods.destroy', $method->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucune méthode de paiement trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile / small screens: card view -->
            <div class="d-lg-none">
                @forelse($paymentMethods as $method)
                    <div class="card border mb-3 shadow-none">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    @if($method->getPhoto())
                                        <img src="{{ $method->getPhoto()->getImageUrl(80,80) }}" alt="{{ $method->method_name }}" class="img-thumbnail me-2" style="width: 44px; height: 44px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $method->method_name }}</h6>
                                        <small class="text-muted">#{{ $method->id }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-{{ $method->is_active ? 'success' : 'danger' }}">
                                    {{ $method->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>

                            @if($method->description)
                                <p class="text-muted small mb-2">{{ Str::limit($method->description, 80) }}</p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <div>
                                    <small class="text-muted d-block">Frais</small>
                                    <span class="fw-semibold">
                                        @if($method->fee_percentage > 0)
                                            {{ $method->fee_percentage }}%
                                            @if($method->fee > 0)
                                                + {{ number_format($method->fee, 2) }} FCFA
                                            @endif
                                        @elseif($method->fee > 0)
                                            {{ number_format($method->fee, 2) }} FCFA
                                        @else
                                            Gratuit
                                        @endif
                                    </span>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('admin.payment-methods.edit', $method->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletePaymentMethodMobile{{ $method->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Modal de suppression (mobile) -->
                                    <div class="modal fade" id="deletePaymentMethodMobile{{ $method->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer la méthode de paiement "{{ $method->method_name }}" ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('admin.payment-methods.destroy', $method->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Aucune méthode de paiement trouvée</div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($paymentMethods->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $paymentMethods->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
