{{-- Formulaire utilisateur boutique — partagé création / édition. Variable : $user (nullable). --}}
@csrf
@if (isset($user) && $user)
    @method('PUT')
@endif

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="u-name">Nom *</label>
        <input class="ctrl" id="u-name" type="text" name="name" required maxlength="255"
            value="{{ old('name', $user->name ?? '') }}">
        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="u-prenom">Prénom</label>
        <input class="ctrl" id="u-prenom" type="text" name="prenom" maxlength="255"
            value="{{ old('prenom', $user->prenom ?? '') }}">
        @error('prenom') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="u-email">Email *</label>
        <input class="ctrl" id="u-email" type="email" name="email" required maxlength="255"
            value="{{ old('email', $user->email ?? '') }}">
        @error('email') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="u-tel">Téléphone</label>
        <input class="ctrl" id="u-tel" type="text" name="tel" maxlength="50"
            value="{{ old('tel', $user->tel ?? '') }}">
        @error('tel') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="u-pass">Mot de passe {{ isset($user) && $user ? '(vide = inchangé)' : '*' }}</label>
        <input class="ctrl" id="u-pass" type="password" name="password" minlength="8"
            {{ isset($user) && $user ? '' : 'required' }} autocomplete="new-password" placeholder="8 caractères min">
        @error('password') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="u-pass2">Confirmer le mot de passe</label>
        <input class="ctrl" id="u-pass2" type="password" name="password_confirmation" autocomplete="new-password">
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="u-naiss">Date de naissance</label>
        <input class="ctrl" id="u-naiss" type="date" name="date_naiss"
            value="{{ old('date_naiss', isset($user) && $user->date_naiss ? (is_string($user->date_naiss) ? $user->date_naiss : $user->date_naiss->format('Y-m-d')) : '') }}">
        @error('date_naiss') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="u-lieu">Lieu de naissance</label>
        <input class="ctrl" id="u-lieu" type="text" name="lieu_naiss" maxlength="255"
            value="{{ old('lieu_naiss', $user->lieu_naiss ?? '') }}">
        @error('lieu_naiss') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field">
    <label>Adresse</label>
    @php
        $uAdr = $user->adresse ?? [];
        if (is_string($uAdr)) {
            $uAdr = json_decode($uAdr, true) ?: [];
        }
    @endphp
    <div class="dash-grid" style="margin-bottom:10px">
        <input class="ctrl" type="text" name="adresse[line1]" placeholder="Adresse ligne 1"
            value="{{ old('adresse.line1', $uAdr['line1'] ?? ($uAdr['adresse'] ?? '')) }}">
        <input class="ctrl" type="text" name="adresse[line2]" placeholder="Adresse ligne 2"
            value="{{ old('adresse.line2', $uAdr['line2'] ?? '') }}">
    </div>
    <div class="dash-grid" style="margin-bottom:0">
        <input class="ctrl" type="text" name="adresse[city]" placeholder="Ville"
            value="{{ old('adresse.city', $uAdr['city'] ?? '') }}">
        <input class="ctrl" type="text" name="adresse[postal_code]" placeholder="Code postal"
            value="{{ old('adresse.postal_code', $uAdr['postal_code'] ?? '') }}">
    </div>
    @error('adresse') <span class="avis-err">{{ $message }}</span> @enderror
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="u-pays">Pays</label>
        <input class="ctrl" id="u-pays" type="text" name="pays" maxlength="100"
            value="{{ old('pays', $user->pays ?? '') }}">
        @error('pays') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0">
        <label for="u-role">Rôle *</label>
        <select class="ctrl" id="u-role" name="role" required>
            <option value="customer" {{ old('role', $user->role ?? 'customer') === 'customer' ? 'selected' : '' }}>
                Client</option>
            <option value="vendeur" {{ old('role', $user->role ?? '') === 'vendeur' ? 'selected' : '' }}>Vendeur
            </option>
            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        @error('role') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
</div>

<div class="dash-grid">
    <div class="field" style="margin-bottom:0">
        <label for="u-status">Statut *</label>
        <select class="ctrl" id="u-status" name="status" required>
            <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Actif
            </option>
            <option value="inactive" {{ old('status', $user->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactif
            </option>
        </select>
        @error('status') <span class="avis-err">{{ $message }}</span> @enderror
    </div>
    <div class="field" style="margin-bottom:0;justify-content:end">
        <label class="switch"><input type="checkbox" name="email_verified" value="1"
                {{ old('email_verified', isset($user) && $user->email_verified_at ? true : false) ? 'checked' : '' }}>
            Email vérifié</label>
    </div>
</div>

<div class="field">
    <label>Réseaux sociaux</label>
    @php
        $uSoc = $user->social_links ?? [];
        if (is_string($uSoc)) {
            $uSoc = json_decode($uSoc, true) ?: [];
        }
    @endphp
    <div class="dash-grid" style="margin-bottom:10px">
        <input class="ctrl" type="url" name="social_links[facebook]" placeholder="Facebook"
            value="{{ old('social_links.facebook', $uSoc['facebook'] ?? '') }}">
        <input class="ctrl" type="url" name="social_links[instagram]" placeholder="Instagram"
            value="{{ old('social_links.instagram', $uSoc['instagram'] ?? '') }}">
    </div>
    <input class="ctrl" type="url" name="social_links[twitter]" placeholder="Twitter / X"
        value="{{ old('social_links.twitter', $uSoc['twitter'] ?? '') }}">
</div>

<div class="pdp-actions" style="margin-top:18px">
    <button class="btn-solid" type="submit"><svg class="ic">
            <use href="#i-b2-check" />
        </svg> {{ isset($user) && $user ? 'Mettre à jour' : "Créer l'utilisateur" }}</button>
    <a class="btn-line" href="{{ route('admin.users.index') }}">Annuler</a>
</div>
