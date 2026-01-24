@extends('admin.base')

@section('title', 'Liste des marques')

@section('content')

    <div class="content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Listes des Marques @if (session()->has('success')) {{ ' - ' }} {{ session('success') }}  @endif</h4>
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter une marque
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
                                <th>Site web</th>
                                <th>Ordre</th>
                                <th>Produits</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                                <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($brand->getPhoto())
                                                <img src="{{ $brand->getPhoto()->getImageUrl(80,80) }}" alt="{{ $brand->name }}"
                                                    class="img-thumbnail"
                                                    style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                            @endif
                                            {{ $brand->name }}
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($brand->description, 50) }}</td>
                                    <td>
                                        @if ($brand->website)
                                            <a href="{{ $brand->website }}" target="_blank" class="text-decoration-none">
                                                <i class="fas fa-external-link-alt me-1"></i>
                                                {{ Str::limit($brand->website, 30) }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $brand->sort_order }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $brand->products()->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $brand->is_active ? 'success' : 'danger' }}">
                                            {{ $brand->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteBrand{{ $brand->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteBrand{{ $brand->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la marque
                                                            "{{ $brand->name }}" ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('admin.brands.destroy', $brand->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger">Supprimer</button>
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
                                    <td colspan="8" class="text-center">Aucune marque trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>


                    <!-- Pagination -->
                    @if ($brands->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $brands->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


@endsection
