@extends('admin.base')

@section('title', (isset($paymentMethod) ? 'Modifier la méthode' : 'Ajouter une méthode'))

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.payment-methods.index') }}">Moyens de paiement</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">{{ isset($paymentMethod) ? $paymentMethod->method_name : 'Nouvelle' }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>{{ isset($paymentMethod) ? 'Modifier « ' . $paymentMethod->method_name . ' »' : 'Ajouter une méthode de paiement' }}
            </h1>
            <p>Nom, fournisseur, compte à créditer, frais et instructions client.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form
                    action="{{ isset($paymentMethod) ? route('admin.payment-methods.update', $paymentMethod->id) : route('admin.payment-methods.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($paymentMethod))
                        @method('PUT')
                    @endif

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="method_name">Nom de la méthode *</label>
                            <input class="ctrl" id="method_name" type="text" name="method_name" required
                                value="{{ old('method_name', $paymentMethod->method_name ?? '') }}"
                                placeholder="Ex : Orange Money">
                            @error('method_name') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="provider_name">Fournisseur</label>
                            <input class="ctrl" id="provider_name" type="text" name="provider_name"
                                value="{{ old('provider_name', $paymentMethod->provider_name ?? '') }}"
                                placeholder="Ex : Orange">
                            @error('provider_name') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="description">Description</label>
                        <textarea class="ctrl" id="description" name="description" rows="3" style="border-radius:12px;resize:vertical">{{ old('description', $paymentMethod->description ?? '') }}</textarea>
                        @error('description') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="account_number">Numéro / compte à créditer</label>
                            <input class="ctrl" id="account_number" type="text" name="account_number"
                                placeholder="Ex : 07 00 00 00 00"
                                value="{{ old('account_number', $paymentMethod->account_number ?? '') }}">
                            @error('account_number') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="sort_order">Ordre d'affichage</label>
                            <input class="ctrl" id="sort_order" type="number" name="sort_order" min="0"
                                value="{{ old('sort_order', $paymentMethod->sort_order ?? 0) }}">
                            @error('sort_order') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="instructions">Instructions à suivre par le client</label>
                        <textarea class="ctrl" id="instructions" name="instructions" rows="4" style="border-radius:12px;resize:vertical"
                            placeholder="Ex : Envoyez le montant au numéro ci-dessus puis gardez le SMS de confirmation.">{{ old('instructions', $paymentMethod->instructions ?? '') }}</textarea>
                        @error('instructions') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="fee">Frais fixes (FCFA)</label>
                            <input class="ctrl" id="fee" type="number" step="0.01" min="0" name="fee"
                                value="{{ old('fee', $paymentMethod->fee ?? '0') }}">
                            @error('fee') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="fee_percentage">Frais (%)</label>
                            <input class="ctrl" id="fee_percentage" type="number" step="0.01" min="0"
                                max="100" name="fee_percentage"
                                value="{{ old('fee_percentage', $paymentMethod->fee_percentage ?? '0') }}">
                            @error('fee_percentage') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="dash-grid">
                        <div class="field" style="margin-bottom:0">
                            <label for="logo">Logo</label>
                            @if (isset($paymentMethod) && $paymentMethod->getPhoto())
                                <div style="margin-bottom:10px">
                                    <img src="{{ $paymentMethod->getPhoto()->getImageUrl(80, 80) }}" alt="Logo"
                                        style="max-height:50px;border-radius:10px;border:1px solid var(--line)">
                                </div>
                                <label class="switch" style="margin-bottom:10px"><input type="checkbox"
                                        id="remove_logo" name="remove_logo" value="1"> Supprimer le logo</label>
                            @endif
                            <input class="ctrl" type="file" id="logo" name="logo" accept="image/*">
                            @error('logo') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field" style="margin-bottom:0">
                            <label for="config">Configuration (JSON)</label>
                            <textarea class="ctrl" id="config" name="config" rows="4" style="border-radius:12px;font-family:monospace;font-size:12px" placeholder='{"cle": "valeur"}'>{{ old('config', isset($paymentMethod->config) ? json_encode($paymentMethod->config, JSON_PRETTY_PRINT) : '') }}</textarea>
                            @error('config') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label class="switch"><input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $paymentMethod->is_active ?? true) ? 'checked' : '' }}> Méthode
                            active</label>
                    </div>

                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" type="submit"><svg class="ic">
                                <use href="#i-b2-check" />
                            </svg> {{ isset($paymentMethod) ? 'Modifier' : 'Ajouter' }}</button>
                        <a class="btn-line" href="{{ route('admin.payment-methods.index') }}">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
