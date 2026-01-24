@extends('admin.base')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Méthodes de Livraison @if (session()->has('error')) {{ ' - ' }} {{ session('error') }}  @endif @if (session()->has('success')) {{ ' - ' }} {{ session('success') }}  @endif</h4>
            <a href="{{ route('admin.shipping-methods.create') }}" class="btn btn-primary">
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
                            <th>Délai de livraison</th>
                            <th>Prix</th>
                            <th>Seuil livraison gratuite</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shippingMethods as $method)
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
                            <td>
                                @if($method->delivery_time_min && $method->delivery_time_max)
                                    {{ $method->delivery_time_min }}-{{ $method->delivery_time_max }} {{ __($method->delivery_time_unit) }}
                                @else
                                    Non spécifié
                                @endif
                            </td>
                            <td>
                                @if($method->price > 0)
                                    {{ number_format($method->price, 2) }}€
                                @else
                                    Gratuit
                                @endif
                            </td>
                            <td>
                                @if($method->free_shipping_threshold)
                                    {{ number_format($method->free_shipping_threshold, 2) }}€
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $method->is_active ? 'success' : 'danger' }}">
                                    {{ $method->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.shipping-methods.edit', $method->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteShippingMethod{{ $method->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteShippingMethod{{ $method->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer la méthode de livraison "{{ $method->method_name }}" ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('admin.shipping-methods.destroy', $method->id) }}" method="POST">
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
                            <td colspan="7" class="text-center">Aucune méthode de livraison trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($shippingMethods->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $shippingMethods->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
