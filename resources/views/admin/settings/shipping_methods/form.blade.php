@extends('admin.base')

@section('title', (isset($shippingMethod) ? 'Modifier le mode' : 'Ajouter un mode'))

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.shipping-methods.index') }}">Livraison</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ isset($shippingMethod) ? $shippingMethod->method_name : 'Nouveau' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ isset($shippingMethod) ? 'Modifier « ' . $shippingMethod->method_name . ' »' : 'Ajouter un mode de livraison' }}
            </h1>
            <p>Tarif, délais, seuils et zones.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form
                    action="{{ isset($shippingMethod) ? route('admin.shipping-methods.update', $shippingMethod->id) : route('admin.shipping-methods.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($shippingMethod))
                        @method('PUT')
                    @endif

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="method_name">Nom du mode *</label>
                            <input class="ctrl" id="method_name" type="text" name="method_name" required
                                value="{{ old('method_name', $shippingMethod->method_name ?? '') }}"
                                placeholder="Ex : Livraison standard">
                            @error('method_name') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="price">Prix (FCFA) *</label>
                            <input class="ctrl" id="price" type="number" step="0.01" min="0" name="price"
                                required value="{{ old('price', $shippingMethod->price ?? '') }}">
                            @error('price') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="description">Description</label>
                        <textarea class="ctrl" id="description" name="description" rows="2" style="border-radius:12px;resize:vertical">{{ old('description', $shippingMethod->description ?? '') }}</textarea>
                        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label>Délai de livraison</label>
                            <div style="display:flex;gap:8px">
                                <input class="ctrl" type="number" min="0" name="delivery_time_min"
                                    placeholder="Min"
                                    value="{{ old('delivery_time_min', $shippingMethod->delivery_time_min ?? '') }}"
                                    aria-label="Délai minimum">
                                <input class="ctrl" type="number" min="0" name="delivery_time_max"
                                    placeholder="Max"
                                    value="{{ old('delivery_time_max', $shippingMethod->delivery_time_max ?? '') }}"
                                    aria-label="Délai maximum">
                                <select class="ctrl" name="delivery_time_unit" aria-label="Unité">
                                    @foreach (['hours' => 'Heures', 'days' => 'Jours', 'weeks' => 'Semaines'] as $v => $l)
                                        <option value="{{ $v }}"
                                            {{ old('delivery_time_unit', $shippingMethod->delivery_time_unit ?? 'days') === $v ? 'selected' : '' }}>
                                            {{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('delivery_time_min') <span class="avis-err">{{ $message }}</span> @enderror
                            @error('delivery_time_max') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="free_shipping_threshold">Franco dès (FCFA)</label>
                            <input class="ctrl" id="free_shipping_threshold" type="number" step="0.01" min="0"
                                name="free_shipping_threshold"
                                value="{{ old('free_shipping_threshold', $shippingMethod->free_shipping_threshold ?? '') }}"
                                placeholder="Vide = jamais offert">
                            @error('free_shipping_threshold') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="min_order_amount">Commande min. (FCFA)</label>
                            <input class="ctrl" id="min_order_amount" type="number" step="0.01" min="0"
                                name="min_order_amount"
                                value="{{ old('min_order_amount', $shippingMethod->min_order_amount ?? '') }}">
                            @error('min_order_amount') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="weight_limit">Poids max (kg)</label>
                            <input class="ctrl" id="weight_limit" type="number" step="0.01" min="0"
                                name="weight_limit"
                                value="{{ old('weight_limit', $shippingMethod->weight_limit ?? '') }}">
                            @error('weight_limit') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="sort_order">Ordre d'affichage</label>
                            <input class="ctrl" id="sort_order" type="number" min="0" name="sort_order"
                                value="{{ old('sort_order', $shippingMethod->sort_order ?? '0') }}">
                            @error('sort_order') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="logo">Logo</label>
                            @if (isset($shippingMethod) && $shippingMethod->getPhoto())
                                <div style="margin-bottom:10px">
                                    <img src="{{ $shippingMethod->getPhoto()->getImageUrl(80, 80) }}" alt="Logo"
                                        style="max-height:50px;border-radius:10px;border:1px solid var(--line)">
                                </div>
                                <label class="switch" style="margin-bottom:10px"><input type="checkbox"
                                        id="remove_logo" name="remove_logo" value="1"> Supprimer le logo</label>
                            @endif
                            <input class="ctrl" type="file" id="logo" name="logo" accept="image/*">
                            @error('logo') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="zones">Zones (JSON)</label>
                        <textarea class="ctrl" id="zones" name="zones" rows="2" style="border-radius:12px;font-family:monospace;font-size:12px"
                            placeholder='["ML"]'>{{ old('zones', isset($shippingMethod->zones) ? json_encode($shippingMethod->zones, JSON_PRETTY_PRINT) : '') }}</textarea>
                        @error('zones') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="config">Configuration (JSON)</label>
                        <textarea class="ctrl" id="config" name="config" rows="3" style="border-radius:12px;font-family:monospace;font-size:12px">{{ old('config', isset($shippingMethod->config) ? json_encode($shippingMethod->config, JSON_PRETTY_PRINT) : '') }}</textarea>
                        @error('config') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label class="switch"><input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $shippingMethod->is_active ?? true) ? 'checked' : '' }}> Mode
                            actif</label>
                    </div>

                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" type="submit"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg> Enregistrer</button>
                        <a class="btn-line" href="{{ route('admin.shipping-methods.index') }}">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
