@extends('admin.base')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Modifier le coupon #{{ $coupon->id }}</h1>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Modifier "{{ $coupon->code }}"</h5>
            <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
            </span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $coupon->code) }}" required maxlength="20">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                            <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Montant fixe (€)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Valeur <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="value" step="0.01" min="0.01" max="100"
                                   class="form-control @error('value') is-invalid @enderror"
                                   value="{{ old('value', $coupon->value) }}" required>
                            <span class="input-group-text" id="value-type">
                                <span class="percentage-badge {{ $coupon->type !== 'percentage' ? 'd-none' : '' }}">%</span>
                                <span class="fixed-badge {{ $coupon->type !== 'fixed' ? 'd-none' : '' }}">€</span>
                            </span>
                        </div>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Statut</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input"
                                   id="is_active" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} value="1">
                            <label class="form-check-label" for="is_active">
                                Actif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de début <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="starts_at"
                               class="form-control @error('starts_at') is-invalid @enderror"
                               value="{{ old('starts_at', $coupon->starts_at->format('Y-m-d\TH:i')) }}" required>
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date d'expiration <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="expires_at"
                               class="form-control @error('expires_at') is-invalid @enderror"
                               value="{{ old('expires_at', $coupon->expires_at->format('Y-m-d\TH:i')) }}" required>
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Limite d'utilisation (optionnel)</label>
                        <input type="number" name="usage_limit" min="0"
                               class="form-control @error('usage_limit') is-invalid @enderror"
                               value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="Laisser vide = illimité">
                        @error('usage_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Actuel : {{ $coupon->usage_count }} utilisation(s)
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.querySelector('[name="type"]');
    const percentageBadge = document.querySelector('.percentage-badge');
    const fixedBadge = document.querySelector('.fixed-badge');

    function updateValueType() {
        if (typeSelect.value === 'percentage') {
            percentageBadge.classList.remove('d-none');
            fixedBadge.classList.add('d-none');
        } else if (typeSelect.value === 'fixed') {
            percentageBadge.classList.add('d-none');
            fixedBadge.classList.remove('d-none');
        }
    }

    typeSelect.addEventListener('change', updateValueType);
    updateValueType(); // Initial state
});
</script>
@endsection
