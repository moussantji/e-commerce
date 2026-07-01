{{-- Mobile card view for orders (visible only on small screens) --}}
@php
    $orderStatusOptions = [
        'en_attente' => 'En attente',
        'traitement' => 'En traitement',
        'expedie' => 'Expédié',
        'livre' => 'Livré',
        'annule' => 'Annulé',
    ];
    $orderStatusClasses = [
        'en_attente' => 'warning',
        'traitement' => 'info',
        'expedie' => 'primary',
        'livre' => 'success',
        'annule' => 'danger',
    ];
@endphp

<div class="d-lg-none py-3">
    @forelse ($commandes as $commande)
        @php
            $statusClass = $orderStatusClasses[$commande->statut] ?? 'secondary';
            $statusLabel = $orderStatusOptions[$commande->statut] ?? ucfirst(str_replace('_', ' ', $commande->statut));
        @endphp
        <div class="card border mb-3 shadow-none">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <a class="fw-bold text-body-highlight" href="{{ route('admin.orders.show', $commande) }}">#{{ $commande->id }}</a>
                        <div class="text-body-tertiary fs-9">{{ $commande->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <span class="badge badge-phoenix badge-phoenix-{{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-m me-2">
                        <img class="rounded-circle" src="{{ asset('assets/img/team/32.webp') }}" alt="" />
                    </div>
                    <h6 class="mb-0">{{ $commande->user->name ?? 'Client inconnu' }}</h6>
                </div>

                <div class="row g-2 fs-9 border-top pt-2">
                    <div class="col-6">
                        <span class="text-muted d-block">Paiement</span>
                        <span class="fw-semibold">{{ $commande->paiement->method_name ?? 'Non spécifié' }}</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">Livraison</span>
                        <span class="fw-semibold">{{ $commande->livraison->method_name ?? 'Non spécifié' }}</span>
                    </div>
                    <div class="col-12 mt-2">
                        <span class="text-muted d-block">Total</span>
                        <span class="fw-bold text-body-highlight">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                {{-- Édition de l'état de la commande --}}
                <div class="border-top mt-2 pt-2">
                    <label class="text-muted fs-9 mb-1 d-block">État de la commande</label>
                    <form action="{{ route('admin.orders.update-status', $commande) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select form-select-sm">
                            @foreach ($orderStatusOptions as $value => $label)
                                <option value="{{ $value }}" {{ $commande->statut === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                            <i class="fas fa-save me-1"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4">Aucune commande trouvée</div>
    @endforelse
</div>
