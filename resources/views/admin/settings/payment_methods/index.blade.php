@extends('admin.base')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Méthodes de Paiement</h4>
            <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une méthode
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
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
                                    @if($method->logo)
                                        <img src="{{ asset('storage/'.$method->logo) }}" alt="{{ $method->method_name }}" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                    @endif
                                    {{ $method->method_name }}
                                </div>
                            </td>
                            <td>{{ Str::limit($method->description, 50) }}</td>
                            <td>
                                @if($method->fee_percentage > 0)
                                    {{ $method->fee_percentage }}%
                                    @if($method->fee > 0)
                                        + {{ number_format($method->fee, 2) }}€
                                    @endif
                                @elseif($method->fee > 0)
                                    {{ number_format($method->fee, 2) }}€
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
                
                <!-- Pagination -->
                @if($paymentMethods->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $paymentMethods->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
