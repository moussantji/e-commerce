@extends('admin.base')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">{{ isset($shippingMethod) ? 'Modifier' : 'Ajouter' }} une méthode de livraison</h4>
        </div>
        <div class="card-body">
            <form action="{{ isset($shippingMethod) ? route('admin.shipping-methods.update', $shippingMethod->id) : route('admin.shipping-methods.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($shippingMethod))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="method_name" class="form-label">Nom de la méthode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('method_name') is-invalid @enderror" id="method_name"
                               name="method_name" value="{{ old('method_name', $shippingMethod->method_name ?? '') }}" required>
                        @error('method_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">Prix (€) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price"
                                   value="{{ old('price', $shippingMethod->price ?? '0') }}" required>
                            <span class="input-group-text">€</span>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="2">{{ old('description', $shippingMethod->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="delivery_time_min" class="form-label">Délai min (jours)</label>
                        <input type="number" min="0" class="form-control @error('delivery_time_min') is-invalid @enderror"
                               id="delivery_time_min" name="delivery_time_min"
                               value="{{ old('delivery_time_min', $shippingMethod->delivery_time_min ?? '') }}">
                        @error('delivery_time_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="delivery_time_max" class="form-label">Délai max (jours)</label>
                        <input type="number" min="0" class="form-control @error('delivery_time_max') is-invalid @enderror"
                               id="delivery_time_max" name="delivery_time_max"
                               value="{{ old('delivery_time_max', $shippingMethod->delivery_time_max ?? '') }}">
                        @error('delivery_time_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="delivery_time_unit" class="form-label">Unité de temps</label>
                        <select class="form-select @error('delivery_time_unit') is-invalid @enderror"
                                id="delivery_time_unit" name="delivery_time_unit">
                            <option value="days" {{ old('delivery_time_unit', $shippingMethod->delivery_time_unit ?? '') == 'days' ? 'selected' : '' }}>Jours</option>
                            <option value="hours" {{ old('delivery_time_unit', $shippingMethod->delivery_time_unit ?? '') == 'hours' ? 'selected' : '' }}>Heures</option>
                            <option value="weeks" {{ old('delivery_time_unit', $shippingMethod->delivery_time_unit ?? '') == 'weeks' ? 'selected' : '' }}>Semaines</option>
                        </select>
                        @error('delivery_time_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="free_shipping_threshold" class="form-label">Seuil livraison gratuite (€)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control @error('free_shipping_threshold') is-invalid @enderror"
                                   id="free_shipping_threshold" name="free_shipping_threshold"
                                   value="{{ old('free_shipping_threshold', $shippingMethod->free_shipping_threshold ?? '') }}">
                            <span class="input-group-text">€</span>
                            @error('free_shipping_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Laissez vide si non applicable</div>
                    </div>

                    <div class="col-md-4">
                        <label for="min_order_amount" class="form-label">Montant minimum de commande (€)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" class="form-control @error('min_order_amount') is-invalid @enderror"
                                   id="min_order_amount" name="min_order_amount"
                                   value="{{ old('min_order_amount', $shippingMethod->min_order_amount ?? '0') }}">
                            <span class="input-group-text">€</span>
                            @error('min_order_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="weight_limit" class="form-label">Poids maximum (kg)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" class="form-control @error('weight_limit') is-invalid @enderror"
                                   id="weight_limit" name="weight_limit"
                                   value="{{ old('weight_limit', $shippingMethod->weight_limit ?? '') }}">
                            <span class="input-group-text">kg</span>
                            @error('weight_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                   value="1" {{ (old('is_active', $shippingMethod->is_active ?? true) ? 'checked' : '') }}>
                            <label class="form-check-label" for="is_active">Méthode active</label>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', $shippingMethod->sort_order ?? '0') }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                            @if(isset($shippingMethod) && $shippingMethod->getPhoto())
                                <div class="mt-2">
                                    <img src="{{ $shippingMethod->getPhoto()->getImageUrl(80,80) }}"
                                         alt="{{ $shippingMethod->method_name }}"
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
                    <label for="zones" class="form-label">Zones de livraison (JSON)</label>
                    <textarea class="form-control font-monospace @error('zones') is-invalid @enderror"
                              id="zones" name="zones" rows="3">{{ old('zones', isset($shippingMethod->zones) ? json_encode($shippingMethod->zones, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <div class="form-text">Exemple: ["FR", "BE", "LU"] pour la France, Belgique et Luxembourg</div>
                    @error('zones')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="config" class="form-label">Configuration (JSON)</label>
                    <textarea class="form-control font-monospace @error('config') is-invalid @enderror"
                              id="config" name="config" rows="5">{{ old('config', isset($shippingMethod->config) ? json_encode($shippingMethod->config, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <div class="form-text">Configuration spécifique au transporteur au format JSON</div>
                    @error('config')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.shipping-methods.index') }}" class="btn btn-secondary">
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

            // Validation du champ price
            const price = document.getElementById('price');
            if (!price.value.trim() || isNaN(parseFloat(price.value)) || parseFloat(price.value) < 0) {
                price.classList.add('is-invalid');
                isValid = false;
            } else {
                price.classList.remove('is-invalid');
            }

            // Validation du JSON des zones
            const zones = document.getElementById('zones');
            if (zones.value.trim()) {
                try {
                    JSON.parse(zones.value);
                    zones.classList.remove('is-invalid');
                } catch (e) {
                    zones.classList.add('is-invalid');
                    isValid = false;
                }
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

        // Afficher/masquer les champs en fonction du type de méthode
        function toggleMethodFields() {
            // Vous pouvez ajouter ici une logique pour afficher/masquer des champs en fonction du type de méthode
        }

        // Écouter les changements sur les champs pertinents
        document.querySelectorAll('select[name="method_type"]').forEach(select => {
            select.addEventListener('change', toggleMethodFields);
        });

        // Initialiser l'état des champs
        toggleMethodFields();
    });
</script>
@endpush
@endsection
