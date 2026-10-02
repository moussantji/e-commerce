{{-- Modale connexion / compte du template : ouverte depuis l'icône compte de l'en-tête. --}}
@php
    $mUser = auth()->user();
    $lmErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    // Réoverture auto après un échec de CE formulaire (pas register/reset).
    $authFormRoutes = ['register', 'password.request', 'password.email', 'password.reset', 'password.store'];
    $loginFailed = !$mUser && !$lmErrors->isEmpty() && ($lmErrors->has('email') || $lmErrors->has('password'))
        && old('email') !== null && !in_array(Route::currentRouteName(), $authFormRoutes, true);
    $mOrders = $mUser ? $mUser->orders()->count() : 0;
    $mFavs = $mUser ? $mUser->wishlistProducts()->count() : 0;
@endphp
<div class="modal{{ $loginFailed ? ' on' : '' }}" id="loginModal" aria-hidden="{{ $loginFailed ? 'false' : 'true' }}">
    <div class="box" role="dialog" aria-modal="true" aria-label="{{ $mUser ? 'Mon compte' : 'Se connecter' }}">
        @auth
            <h2>Bonjour {{ $mUser->prenom ?? $mUser->name }}</h2>
            <p class="sub">{{ $mUser->email }} — {{ $mOrders }} commande{{ $mOrders > 1 ? 's' : '' }} ·
                {{ $mFavs }} favori{{ $mFavs > 1 ? 's' : '' }}</p>
            <div class="foot">
                <a class="btn-solid" style="flex:1"
                    href="{{ $mUser->isAdmin() ? route('admin.dashboard') : ($mUser->role === 'vendeur' ? route('vendeur.dashboard') : route('dashboard')) }}">Mon
                    espace</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button class="btn-line" type="submit">Se déconnecter</button>
                </form>
            </div>
            <div class="foot">
                <a class="btn-line" style="flex:1" href="{{ route('favoris') }}">Mes favoris</a>
                <button class="btn-line" style="flex:1" type="button" data-close-modal>Fermer</button>
            </div>
        @else
            <h2>Se connecter</h2>
            <p class="sub">Accédez à vos favoris et au suivi de vos commandes.</p>
            <form method="POST" action="{{ route('login') }}" style="margin-top:18px" id="loginModalForm" novalidate>
                @csrf
                <div class="field {{ $lmErrors->has('email') ? 'bad' : '' }}">
                    <label for="lmMail">Email</label>
                    <input class="ctrl" id="lmMail" type="email" name="email" value="{{ old('email') }}"
                        placeholder="vous@email.com" autocomplete="username">
                    @if ($lmErrors->has('email'))
                        <span class="avis-err">{{ $lmErrors->first('email') }}</span>
                    @endif
                </div>
                <div class="field {{ $lmErrors->has('password') ? 'bad' : '' }}">
                    <label for="lmMdp">Mot de passe</label>
                    <input class="ctrl" id="lmMdp" type="password" name="password" placeholder="••••••••"
                        autocomplete="current-password">
                    @if ($lmErrors->has('password'))
                        <span class="avis-err">{{ $lmErrors->first('password') }}</span>
                    @endif
                </div>
                <div class="field" id="lmErr" style="display:none">
                    <div class="err" id="lmErrTxt"></div>
                </div>
                <label class="switch" style="margin-bottom:14px"><input type="checkbox" name="remember" checked> Se
                    souvenir de moi</label>
                <div class="pdp-actions">
                    <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                            <use href="#i-user" />
                        </svg> Se connecter</button>
                </div>
            </form>
            <div
                style="display:flex;align-items:center;gap:12px;margin:16px 0 12px;font-size:12px;color:var(--grey)">
                <span style="flex:1;height:1px;background:var(--line)"></span> ou <span
                    style="flex:1;height:1px;background:var(--line)"></span>
            </div>
            <div class="pdp-actions">
                <a class="btn-line" style="flex:1" href="{{ url('/auth/google/redirect') }}"><span
                        style="font-weight:800;background:linear-gradient(135deg,#4285F4,#EA4335,#FBBC05,#34A853);-webkit-background-clip:text;background-clip:text;color:transparent">G</span>
                    Continuer avec Google</a>
            </div>
            <div class="pdp-actions" style="margin-top:10px">
                <button class="btn-line" style="flex:1" type="button" data-open-vendeur"><svg class="ic">
                        <use href="#i-store" />
                    </svg> Créer un compte vendeur</button>
            </div>
            <p class="muted-sm" style="text-align:center;margin-top:14px">
                <a class="lien" href="{{ route('password.request') }}">Mot de passe oublié ?</a> ·
                <a class="lien" href="{{ route('register') }}">Créer un compte</a>
            </p>
        @endauth
    </div>
</div>

@guest
    @php
        $vOpen = $lmErrors->hasBag('vendeur') && $lmErrors->vendeur->any();
        $vCreated = session('vendeur_created');
    @endphp
    {{-- Modale demande de compte vendeur --}}
    <div class="modal{{ $vOpen ? ' on' : '' }}" id="vendeurModal"
        aria-hidden="{{ $vOpen ? 'false' : 'true' }}">
        <div class="box" role="dialog" aria-modal="true" aria-label="Créer un compte vendeur">
            <h2>Devenir vendeur</h2>
            <p class="sub">Remplissez vos informations. Compte créé <b>en attente de validation</b> par notre équipe.
            </p>
            <form method="POST" action="{{ route('vendeur.demande') }}" style="margin-top:18px" id="vendeurModalForm"
                novalidate>
                @csrf
                <div class="field">
                    <label for="vmName">Nom complet *</label>
                    <input class="ctrl" id="vmName" type="text" name="name" value="{{ old('name') }}"
                        placeholder="Ex. Ibrahim Sangaré" required autocomplete="name">
                    @if ($lmErrors->hasBag('vendeur') && $lmErrors->vendeur->has('name'))
                        <span class="avis-err">{{ $lmErrors->vendeur->first('name') }}</span>
                    @endif
                </div>
                <div class="field">
                    <label for="vmMail">Adresse email *</label>
                    <input class="ctrl" id="vmMail" type="email" name="email" value="{{ old('email') }}"
                        placeholder="vous@email.com" required autocomplete="email">
                    @if ($lmErrors->hasBag('vendeur') && $lmErrors->vendeur->has('email'))
                        <span class="avis-err">{{ $lmErrors->vendeur->first('email') }}</span>
                    @endif
                </div>
                <div class="field">
                    <label for="vmTel">Téléphone (Mobile Money) *</label>
                    <input class="ctrl" id="vmTel" type="tel" name="tel" value="{{ old('tel') }}"
                        placeholder="Ex : 70 00 00 00" required autocomplete="tel">
                    @if ($lmErrors->hasBag('vendeur') && $lmErrors->vendeur->has('tel'))
                        <span class="avis-err">{{ $lmErrors->vendeur->first('tel') }}</span>
                    @endif
                </div>
                <div class="field">
                    <label for="vmMdp">Mot de passe * <small style="color:var(--grey)">(8 caractères min)</small></label>
                    <input class="ctrl" id="vmMdp" type="password" name="password" placeholder="••••••••" required
                        minlength="8" autocomplete="new-password">
                    @if ($lmErrors->hasBag('vendeur') && $lmErrors->vendeur->has('password'))
                        <span class="avis-err">{{ $lmErrors->vendeur->first('password') }}</span>
                    @endif
                </div>
                <div class="field">
                    <label for="vmMdp2">Confirmer le mot de passe *</label>
                    <input class="ctrl" id="vmMdp2" type="password" name="password_confirmation"
                        placeholder="••••••••" required autocomplete="new-password">
                </div>
                <div class="field" id="vmErr" style="display:none">
                    <div class="err" id="vmErrTxt"></div>
                </div>
                <div class="pdp-actions">
                    <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                            <use href="#i-store" />
                        </svg> Envoyer ma demande</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Popup de confirmation : compte créé + redirection WhatsApp --}}
    @if (!empty($vCreated))
        <div class="modal on" id="vendeurSuccess" aria-hidden="false">
            <div class="box" role="dialog" aria-modal="true" aria-label="Compte vendeur créé">
                <div style="text-align:center">
                    <span
                        style="display:inline-grid;place-items:center;width:64px;height:64px;border-radius:50%;background:#d1fae5;color:#047857"><svg
                            class="ic" style="width:30px;height:30px">
                            <use href="#i-b2-check" />
                        </svg></span>
                </div>
                <h2 style="text-align:center;margin-top:12px">Compte créé !</h2>
                <p class="sub" style="text-align:center">Bienvenue {{ $vCreated['nom'] ?? '' }} ({{ $vCreated['email'] ?? '' }} ·
                    {{ $vCreated['tel'] ?? '' }}). Votre compte est <b>en attente de validation</b> par notre équipe.
                </p>
                <div class="tagline-band" style="margin:14px 0 0">
                    <svg class="ic">
                        <use href="#i-b2-info" />
                    </svg>
                    <span>Redirection vers WhatsApp dans <b id="vendeurCount">8</b> s pour finaliser avec vos
                        informations...</span>
                </div>
                <div class="pdp-actions" style="margin-top:16px">
                    <a class="btn-solid" style="flex:1;background:#22c55e" href="{{ $vCreated['wa'] }}" target="_blank"
                        rel="noopener"><svg class="ic" style="fill:currentColor;stroke:none">
                            <use href="#i-whatsapp" />
                        </svg> Continuer sur WhatsApp</a>
                </div>
            </div>
        </div>
        <script>
            (function() {
                var n = 8,
                    el = document.getElementById('vendeurCount'),
                    url = @json($vCreated['wa']),
                    done = false;
                var iv = setInterval(function() {
                    n--;
                    if (el) el.textContent = n;
                    if (n <= 0 && !done) {
                        done = true;
                        clearInterval(iv);
                        window.location.href = url;
                    }
                }, 1000);
            })();
        </script>
    @endif
@endguest
