{{-- resources/views/admin/caracteristiques/index.blade.php --}}
@extends('admin.base')

@section('title', 'Liste des caractéristiques')

@section('content')
    <div class="content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Liste des Caractéristiques @if (session()->has('success'))
                        {{ ' - ' }} {{ session('success') }}
                    @endif
                </h4>
                <a href="{{ route('admin.caracteristiques.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter une caractéristique
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Type</th> <!-- ✅ NOUVEAU -->
                                <th>Unité</th> <!-- ✅ MODIFIÉ -->
                                <th>Filtrable</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($caracteristiques as $caracteristique)
                                <tr>
                                    <td>{{ $caracteristique->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{ $caracteristique->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $caracteristique->type ?: '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $caracteristique->unite ?: '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $caracteristique->is_filterable ? 'success' : 'danger' }}">
                                            {{ $caracteristique->is_filterable ? 'Oui' : 'Non' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.caracteristiques.edit', $caracteristique->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteCaracteristique{{ $caracteristique->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteCaracteristique{{ $caracteristique->id }}"
                                                tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la caractéristique
                                                            "{{ $caracteristique->name }}" ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Annuler</button>
                                                            <form
                                                                action="{{ route('admin.caracteristiques.destroy', $caracteristique->id) }}"
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
                                    <td colspan="6" class="text-center">Aucune caractéristique trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    @if ($caracteristiques->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $caracteristiques->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
