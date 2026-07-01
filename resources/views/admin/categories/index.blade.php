@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des catégories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle catégorie
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Hiérarchie</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->getPhoto())
                                        <img src="{{ $category->getPhoto()->getImageUrl(48, 48) }}"
                                             alt="{{ $category->name }}"
                                             class="rounded" style="width:48px;height:48px;object-fit:cover;">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded bg-body-secondary text-body-tertiary"
                                              style="width:48px;height:48px;">
                                            <i class="fas fa-image"></i>
                                        </span>
                                    @endif
                                </td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">Catégorie principale</span>
                                    @if($category->children->count())
                                        <span class="badge bg-secondary">{{ $category->children->count() }} sous-catégorie{{ $category->children->count() > 1 ? 's' : '' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Sous-catégories, affichées en retrait sous leur parent --}}
                            @foreach($category->children as $child)
                                <tr class="table-light">
                                    <td>{{ $child->id }}</td>
                                    <td>
                                        @if($child->getPhoto())
                                            <img src="{{ $child->getPhoto()->getImageUrl(40, 40) }}"
                                                 alt="{{ $child->name }}"
                                                 class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                        @else
                                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-body-secondary text-body-tertiary"
                                                  style="width:40px;height:40px;">
                                                <i class="fas fa-image"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted me-1">&#8627;</span>{{ $child->name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info-emphasis">
                                            Sous-catégorie de {{ $category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $child->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.edit', $child) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $child) }}" method="POST"
                                                  onsubmit="return confirm('Supprimer cette sous-catégorie ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune catégorie trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
