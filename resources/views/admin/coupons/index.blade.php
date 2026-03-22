@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des coupons</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau coupon
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
                            <th>Code</th>
                            <th>Type</th>
                            <th>Valeur</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Utilisations</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->id }}</td>
                                <td>
                                    <strong>{{ $coupon->code }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($coupon->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }} %
                                    @else
                                        {{ number_format($coupon->value, 2) }} FCFA
                                    @endif
                                </td>
                                <td>{{ $coupon->starts_at->format('d/m/Y') }}</td>
                                <td>{{ $coupon->expires_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $coupon->usage_count >= ($coupon->usage_limit ?? 0) ? 'bg-warning' : 'bg-success' }}">
                                        {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce coupon ?');">
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
                                <td colspan="9" class="text-center">Aucun coupon trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
