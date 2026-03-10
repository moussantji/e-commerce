@extends('admin.base')

@section('title', '')

@section('content')

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des bannières</h1>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle bannière
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre principal</th>
                            <th>Titre secondaire</th>
                            <th>Pourcentage</th>
                            <th>Bouton</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td>
                                    <strong>{{ $banner->title1_short }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $banner->title2_short }}</strong>
                                </td>
                                <td>
                                    @if($banner->percentage)
                                        <span class="badge bg-warning">
                                            {{ $banner->percentage }} %
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Aucun</span>
                                    @endif
                                </td>
                                <td>
                                    @if($banner->button_link)
                                        <strong>{{ $banner->button_link }}</strong>
                                    @else
                                        <span class="badge bg-secondary">Sans bouton</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $banner->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $banner->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.banners.edit', $banner) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette bannière ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune bannière trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@endsection
