{{-- Formulaire coupon boutique — partagé création / édition. Variable : $coupon (nullable). --}}
@csrf
@if (isset($coupon) && $coupon)
    @method('PUT')
@endif

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="cp-code">Code *</label>
        <input class="ctrl" id="cp-code" type="text" name="code" required maxlength="50"
            placeholder="Ex : BIENVENUE10" value="{{ old('code', $coupon->code ?? '') }}"
            style="text-transform:uppercase">
        @error('code') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="cp-type">Type *</label>
        <select class="ctrl" id="cp-type" name="type" required>
            <option value="percentage" {{ old('type', $coupon->type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>
                Pourcentage (%)</option>
            <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>Montant fixe
                (FCFA)</option>
        </select>
        @error('type') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="cp-value">Valeur * <small style="color:var(--grey)" id="cp-value-hint">(% si pourcentage)</small></label>
        <input class="ctrl" id="cp-value" type="number" name="value" step="0.01" min="0.01" required
            value="{{ old('value', $coupon->value ?? '') }}">
        @error('value') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="cp-limit">Limite d'utilisation</label>
        <input class="ctrl" id="cp-limit" type="number" name="usage_limit" min="1"
            placeholder="Illimitée si vide" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}">
        @error('usage_limit') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="cp-start">Date de début *</label>
        <input class="ctrl" id="cp-start" type="datetime-local" name="starts_at" required
            value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
        @error('starts_at') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="cp-end">Date de fin *</label>
        <input class="ctrl" id="cp-end" type="datetime-local" name="expires_at" required
            value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
        @error('expires_at') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field">
    <label class="switch"><input type="checkbox" name="is_active" value="1"
            {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}> Coupon actif</label>
</div>

<div class="pdp-actions" style="margin-top:18px">
    <button class="btn-solid" type="submit"><svg class="ic">
            <use href="#i-b2-check" />
        </svg> {{ isset($coupon) && $coupon ? 'Mettre à jour' : 'Créer le coupon' }}</button>
    <a class="btn-line" href="{{ route('admin.coupons.index') }}">Annuler</a>
</div>

<script>
    (function() {
        var type = document.getElementById('cp-type');
        var hint = document.getElementById('cp-value-hint');
        var value = document.getElementById('cp-value');
        function maj() {
            var pct = type.value === 'percentage';
            hint.textContent = pct ? '(% si pourcentage)' : '(FCFA si montant fixe)';
            if (pct) {
                value.setAttribute('max', '100');
            } else {
                value.removeAttribute('max');
            }
        }
        if (type) {
            type.addEventListener('change', maj);
            maj();
        }
    })();
</script>
