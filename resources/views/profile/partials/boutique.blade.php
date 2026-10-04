{{-- Bloc "Mon profil" style boutique (accueil) — partagé client + admin.
    Variables : $pfUser, $updateRoute, $passwordRoute, $deleteRoute (nullable),
    $backRoute, $backLabel, $roleLabel --}}
@php
    $initiales = mb_strtoupper(mb_substr($pfUser->prenom ?? $pfUser->name ?? '?', 0, 1) . mb_substr(explode(' ', $pfUser->name ?? '')[1] ?? '', 0, 1));
    $photo = method_exists($pfUser, 'getPhoto') && $pfUser->getPhoto() ? $pfUser->getPhoto()->getImageUrl(300, 300) : null;
    $dateNaiss = $pfUser->date_naiss ? (is_string($pfUser->date_naiss) ? \Carbon\Carbon::parse($pfUser->date_naiss)->format('Y-m-d') : $pfUser->date_naiss->format('Y-m-d')) : '';
    $adr = $pfUser->adresse ?? null;
    $adrText = is_array($adr) ? ($adr['adresse'] ?? '') : (is_string($adr) ? (json_decode($adr, true)['adresse'] ?? $adr) : '');
    $soc = $pfUser->social_links ?? [];
    if (is_string($soc)) {
        $soc = json_decode($soc, true) ?: [];
    }
    $soc = is_array($soc) ? $soc : [];
    $defaultTab = 'info';
    if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')) {
        $defaultTab = 'password';
    } elseif (isset($errors) && method_exists($errors, 'hasBag') && $errors->hasBag('userDeletion') && $errors->userDeletion->any()) {
        $defaultTab = 'delete';
    }
@endphp

<div class="dash-head">
    <div class="dash-who">
        @if ($photo)
            <img src="{{ $photo }}" alt="Photo de {{ $pfUser->name }}"
                style="width:64px;height:64px;border-radius:50%;object-fit:cover;flex:none;box-shadow:var(--shadow)">
        @else
            <span class="av-lg">{{ $initiales }}</span>
        @endif
        <div>
            <h1>{{ $pfUser->prenom ?? $pfUser->name }} {{ $pfUser->name && $pfUser->prenom ? '(' . $pfUser->name . ')' : '' }}</h1>
            <p>{{ $pfUser->email }} · {{ $roleLabel ?? 'client' }} depuis
                {{ $pfUser->created_at?->format('d/m/Y') }}</p>
        </div>
    </div>
    <div class="pdp-actions" style="margin:0">
        <a class="btn-line" href="{{ route($backRoute) }}"><svg class="ic">
                <use href="#i-chevron" />
            </svg> {{ $backLabel }}</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button class="btn-line" type="submit"><svg class="ic">
                    <use href="#i-user" />
                </svg> Se déconnecter</button>
        </form>
    </div>
</div>

<div class="tabs">
    <div class="tabs-head">
        <button type="button" class="tab-btn{{ $defaultTab === 'info' ? ' on' : '' }}"
            data-pftab="info">Informations</button>
        <button type="button" class="tab-btn{{ $defaultTab === 'password' ? ' on' : '' }}"
            data-pftab="password">Mot de passe</button>
        @if (!empty($deleteRoute))
            <button type="button" class="tab-btn{{ $defaultTab === 'delete' ? ' on' : '' }}"
                data-pftab="delete">Supprimer le compte</button>
        @endif
    </div>
    <div class="tabs-body">
        <div data-pfbody="info" @if ($defaultTab !== 'info') style="display:none" @endif>
            <form method="POST" action="{{ route($updateRoute) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="field">
                    <label for="pf-avatar">Photo de profil</label>
                    <label class="dropzone" for="pf-avatar" style="cursor:pointer">
                        <svg class="ic">
                            <use href="#i-user" />
                        </svg>
                        <span>Cliquez pour changer votre photo<br><small style="color:var(--grey)">JPG, PNG — 2 Mo
                                max</small></span>
                    </label>
                    <input id="pf-avatar" type="file" name="avatar" accept="image/*" hidden>
                    @error('avatar') <span class="avis-err">{{ $message }}</span> @enderror
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-name">Nom *</label>
                        <input id="pf-name" class="ctrl" name="name" type="text" required
                            value="{{ old('name', $pfUser->name) }}">
                        @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-prenom">Prénom</label>
                        <input id="pf-prenom" class="ctrl" name="prenom" type="text"
                            value="{{ old('prenom', $pfUser->prenom) }}">
                        @error('prenom') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-email">Email *</label>
                        @if (!empty($emailLocked))
                            <input id="pf-email" class="ctrl" type="email" value="{{ $pfUser->email }}" readonly
                                style="background:var(--lav-1);color:var(--grey);cursor:not-allowed">
                            <span class="muted-sm">Identifiant du compte — pour le modifier, contactez le support au
                                <a class="lien" href="tel:+22382019583">+223 82 01 95 83</a>.</span>
                        @else
                            <input id="pf-email" class="ctrl" name="email" type="email" required
                                value="{{ old('email', $pfUser->email) }}">
                            @error('email') <span class="avis-err">{{ $message }}</span> @enderror
                        @endif
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-tel">Téléphone (WhatsApp)</label>
                        <input id="pf-tel" class="ctrl" name="tel" type="tel"
                            value="{{ old('tel', $pfUser->tel) }}" placeholder="Ex : 70 00 00 00">
                        <small style="color:var(--grey)">Numéro WhatsApp : le code de vérification y sera envoyé.</small>
                        @error('tel') <span class="avis-err">{{ $message }}</span> @enderror
                        @if (!empty($phoneVerify))
                            <div style="margin-top:8px">
                                @if ($pfUser->tel_verified_at)
                                    <span class="st ok">Numéro vérifié le
                                        {{ $pfUser->tel_verified_at->format('d/m/Y') }}</span>
                                @else
                                    <span class="st conf">Numéro non vérifié</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-naiss">Date de naissance</label>
                        <input id="pf-naiss" class="ctrl" name="date_naiss" type="date"
                            value="{{ old('date_naiss', $dateNaiss) }}">
                        @error('date_naiss') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-lieu">Lieu de naissance</label>
                        <input id="pf-lieu" class="ctrl" name="lieu_naiss" type="text"
                            value="{{ old('lieu_naiss', $pfUser->lieu_naiss) }}">
                        @error('lieu_naiss') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-pays">Pays</label>
                        <input id="pf-pays" class="ctrl" name="pays" type="text"
                            value="{{ old('pays', $pfUser->pays) }}" placeholder="Mali">
                        @error('pays') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-region">Région</label>
                        <input id="pf-region" class="ctrl" name="region" type="text"
                            value="{{ old('region', $pfUser->region) }}" placeholder="Bamako">
                        @error('region') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="field">
                    <label for="pf-adresse">Adresse complète</label>
                    <textarea id="pf-adresse" class="ctrl" name="adresse" rows="2" style="border-radius:12px;resize:vertical">{{ old('adresse', $adrText) }}</textarea>
                    @error('adresse') <span class="avis-err">{{ $message }}</span> @enderror
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-fb">Facebook</label>
                        <input id="pf-fb" class="ctrl" name="facebook_url" type="url"
                            value="{{ old('facebook_url', $soc['facebook'] ?? '') }}"
                            placeholder="https://facebook.com/votrepseudo">
                        @error('facebook_url') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-ig">Instagram</label>
                        <input id="pf-ig" class="ctrl" name="instagram_url" type="url"
                            value="{{ old('instagram_url', $soc['instagram'] ?? '') }}"
                            placeholder="https://instagram.com/votrepseudo">
                        @error('instagram_url') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-tw">Twitter / X</label>
                        <input id="pf-tw" class="ctrl" name="twitter_url" type="url"
                            value="{{ old('twitter_url', $soc['twitter'] ?? '') }}"
                            placeholder="https://x.com/votrepseudo">
                        @error('twitter_url') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-li">LinkedIn</label>
                        <input id="pf-li" class="ctrl" name="linkedin_url" type="url"
                            value="{{ old('linkedin_url', $soc['linkedin'] ?? '') }}"
                            placeholder="https://linkedin.com/in/votrepseudo">
                        @error('linkedin_url') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="pdp-actions" style="margin-top:18px">
                    <button class="btn-solid" type="submit"><svg class="ic">
                            <use href="#i-b2-check" />
                        </svg> Enregistrer les modifications</button>
                </div>
            </form>
        </div>
        @if (!empty($phoneVerify) && empty($pfUser->tel_verified_at))
            <div data-pfbody="info" @if ($defaultTab !== 'info') style="display:none" @endif>
                <div class="tagline-band" style="margin-top:16px">
                    <svg class="ic">
                        <use href="#i-card" />
                    </svg>
                    <span><b>Vérifiez votre numéro WhatsApp</b> pour sécuriser vos commandes et paiements Mobile Money.</span>
                </div>
                <div class="dash-grid" style="margin-bottom:0">
                    <form method="POST" action="{{ route('profile.phone.send') }}">
                        @csrf
                        <button class="btn-line" type="submit" style="width:100%">Recevoir le code sur WhatsApp</button>
                    </form>
                    <form method="POST" action="{{ route('profile.phone.verify') }}">
                        @csrf
                        <div style="display:flex;gap:8px">
                            <input class="ctrl" name="code" inputmode="numeric" maxlength="6" placeholder="Code à 6 chiffres"
                                required style="border-radius:12px;flex:1">
                            <button class="btn-solid" type="submit"
                                style="font-size:13.5px;padding:11px 20px">Vérifier</button>
                        </div>
                        @error('code') <span class="avis-err">{{ $message }}</span> @enderror
                    </form>
                </div>
            </div>
        @endif
        <div data-pfbody="password" @if ($defaultTab !== 'password') style="display:none" @endif>
            <form method="POST" action="{{ route($passwordRoute) }}">
                @csrf
                @method('PUT')
                <div class="field">
                    <label for="pf-current">Mot de passe actuel *</label>
                    <input id="pf-current" class="ctrl" name="current_password" type="password" required
                        autocomplete="current-password" placeholder="••••••••">
                    @error('current_password') <span class="avis-err">{{ $message }}</span> @enderror
                </div>
                <div class="dash-grid">
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-new">Nouveau mot de passe * <small
                                style="color:var(--grey)">(8 caractères min)</small></label>
                        <input id="pf-new" class="ctrl" name="password" type="password" required minlength="8"
                            autocomplete="new-password" placeholder="••••••••">
                        @error('password') <span class="avis-err">{{ $message }}</span> @enderror
                    </div>
                    <div class="field" style="margin-bottom:0">
                        <label for="pf-confirm">Confirmer le mot de passe *</label>
                        <input id="pf-confirm" class="ctrl" name="password_confirmation" type="password" required
                            autocomplete="new-password" placeholder="••••••••">
                    </div>
                </div>
                <div class="pdp-actions" style="margin-top:18px">
                    <button class="btn-solid" type="submit"><svg class="ic">
                            <use href="#i-b2-lock" />
                        </svg> Mettre à jour le mot de passe</button>
                </div>
            </form>
        </div>
        @if (!empty($deleteRoute))
            <div data-pfbody="delete" @if ($defaultTab !== 'delete') style="display:none" @endif>
                <div class="tagline-band"
                    style="background:#ffe4e6;border-color:#fecdd3;color:#be123c;margin:0 0 16px">
                    <svg class="ic">
                        <use href="#i-b2-alert" />
                    </svg>
                    <span><b>Attention, action irréversible.</b> Votre compte, vos favoris et votre historique seront
                        définitivement supprimés.</span>
                </div>
                <form method="POST" action="{{ route($deleteRoute) }}">
                    @csrf
                    @method('DELETE')
                    <div class="field">
                        <label for="pf-delpass">Confirmez avec votre mot de passe *</label>
                        <input id="pf-delpass" class="ctrl" name="password" type="password" required
                            autocomplete="current-password" placeholder="••••••••">
                        @if ($errors->hasBag('userDeletion'))
                            @foreach ($errors->userDeletion->all() as $err)
                                <span class="avis-err">{{ $err }}</span>
                            @endforeach
                        @endif
                    </div>
                    <div class="pdp-actions" style="margin-top:18px">
                        <button class="btn-solid" style="background:var(--pink)" type="submit"
                            onclick="return confirm('Supprimer définitivement votre compte ?')"><svg class="ic">
                                <use href="#i-b2-trash" />
                            </svg> Supprimer mon compte</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    (function() {
        var head = document.querySelector('[data-pftab].on');
        document.querySelectorAll('[data-pftab]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-pftab]').forEach(function(b) {
                    b.classList.remove('on');
                });
                btn.classList.add('on');
                var key = btn.getAttribute('data-pftab');
                document.querySelectorAll('[data-pfbody]').forEach(function(body) {
                    body.style.display = body.getAttribute('data-pfbody') === key ? '' : 'none';
                });
            });
        });
        var avatar = document.getElementById('pf-avatar');
        if (avatar) {
            avatar.addEventListener('change', function() {
                var zone = avatar.closest('.field').querySelector('.dropzone span');
                if (zone && avatar.files.length) {
                    zone.innerHTML = 'Photo choisie : <b>' + avatar.files[0].name + '</b>';
                }
            });
        }
    })();
</script>
