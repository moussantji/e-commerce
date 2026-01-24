@extends('admin.base')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">{{ isset($paymentMethod) ? 'Modifier' : 'Ajouter' }} une méthode de paiement</h4>
        </div>
        <div class="card-body">
            <form action="{{ isset($paymentMethod) ? route('admin.payment-methods.update', $paymentMethod->id) : route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($paymentMethod))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="method_name" class="form-label">Nom de la méthode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('method_name') is-invalid @enderror" id="method_name"
                               name="method_name" value="{{ old('method_name', $paymentMethod->method_name ?? '') }}" required>
                        @error('method_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="provider_name" class="form-label">Fournisseur</label>
                        <input type="text" class="form-control @error('provider_name') is-invalid @enderror"
                               id="provider_name" name="provider_name"
                               value="{{ old('provider_name', $paymentMethod->provider_name ?? '') }}">
                        @error('provider_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description', $paymentMethod->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fee" class="form-label">Frais fixes (€)</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('fee') is-invalid @enderror"
                               id="fee" name="fee" value="{{ old('fee', $paymentMethod->fee ?? '0') }}">
                        @error('fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="fee_percentage" class="form-label">Frais en pourcentage (%)</label>
                        <input type="number" step="0.01" min="0" max="100"
                               class="form-control @error('fee_percentage') is-invalid @enderror"
                               id="fee_percentage" name="fee_percentage"
                               value="{{ old('fee_percentage', $paymentMethod->fee_percentage ?? '0') }}">
                        @error('fee_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="sort_order" class="form-label">Ordre d'affichage</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order"
                               value="{{ old('sort_order', $paymentMethod->sort_order ?? '0') }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" {{ (old('is_active', $paymentMethod->is_active ?? true) ? 'checked' : '') }}>
                            <label class="form-check-label" for="is_active">Méthode active</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input class="form-control @error('logo') is-invalid @enderror"
                                   type="file" id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if(isset($paymentMethod) && $paymentMethod->getPhoto())
                                <div class="mt-2">
                                    <img src="{{ $paymentMethod->getPhoto()->getImageUrl(80,80) }}"
                                         alt="{{ $paymentMethod->method_name }}"
                                         style="max-height: 50px; max-width: 100px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox"
                                               id="remove_logo" name="remove_logo" value="1">
                                        <label class="form-check-label" for="remove_logo">
                                            Supprimer le logo
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="config" class="form-label">Configuration (JSON)</label>
                    <textarea class="form-control font-monospace @error('config') is-invalid @enderror"
                              id="config" name="config" rows="5">{{ old('config', isset($paymentMethod->config) ? json_encode($paymentMethod->config, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <div class="form-text">Configuration spécifique au fournisseur au format JSON</div>
                    @error('config')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Validation du formulaire
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');

        form.addEventListener('submit', function(event) {
            let isValid = true;

            // Validation du champ method_name
            const methodName = document.getElementById('method_name');
            if (!methodName.value.trim()) {
                methodName.classList.add('is-invalid');
                isValid = false;
            } else {
                methodName.classList.remove('is-invalid');
            }

            // Validation du JSON de configuration
            const config = document.getElementById('config');
            if (config.value.trim()) {
                try {
                    JSON.parse(config.value);
                    config.classList.remove('is-invalid');
                } catch (e) {
                    config.classList.add('is-invalid');
                    isValid = false;
                }
            }

            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();

                // Faire défiler jusqu'au premier champ invalide
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
</script>
@endpush
@endsection
