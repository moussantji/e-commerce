{{-- Modale connexion / compte du template : ouverte depuis l'icône compte de l'en-tête. --}}
@php
    $mUser = auth()->user();
    // Réoverture auto après un échec de CE formulaire (pas register/reset).
    $authFormRoutes = ['register', 'password.request', 'password.email', 'password.reset', 'password.store'];
    $loginFailed = !$mUser && !$errors->isEmpty() && ($errors->has('email') || $errors->has('password'))
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
                    href="{{ $mUser->isAdmin() ? route('admin.dashboard') : route('dashboard') }}">Mon espace</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button class="btn-line" type="submit">Se déconnecter</button>
                </form>
            </div>
            @if (($mUser->role ?? '') === 'vendeur')
                <div class="foot">
                    <a class="btn-line" style="flex:1" href="{{ route('vendeur.dashboard') }}">Espace vendeur</a>
                </div>
            @endif
            <div class="foot">
                <a class="btn-line" style="flex:1" href="{{ route('favoris') }}">Mes favoris</a>
                <button class="btn-line" style="flex:1" type="button" data-close-modal>Fermer</button>
            </div>
        @else
            <h2>Se connecter</h2>
            <p class="sub">Accédez à vos favoris et au suivi de vos commandes.</p>
            <form method="POST" action="{{ route('login') }}" style="margin-top:18px" id="loginModalForm" novalidate>
                @csrf
                <div class="field @error('email') bad @enderror">
                    <label for="lmMail">Email</label>
                    <input class="ctrl" id="lmMail" type="email" name="email" value="{{ old('email') }}"
                        placeholder="vous@email.com" autocomplete="username">
                    @error('email') <span class="avis-err">{{ $message }}</span> @enderror
                </div>
                <div class="field @error('password') bad @enderror">
                    <label for="lmMdp">Mot de passe</label>
                    <input class="ctrl" id="lmMdp" type="password" name="password" placeholder="••••••••"
                        autocomplete="current-password">
                    @error('password') <span class="avis-err">{{ $message }}</span> @enderror
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
                    <button class="btn-line" type="button" data-close-modal>Fermer</button>
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
            <p class="muted-sm" style="text-align:center;margin-top:14px">
                <a class="lien" href="{{ route('password.request') }}">Mot de passe oublié ?</a> ·
                <a class="lien" href="{{ route('register') }}">Créer un compte</a>
            </p>
        @endauth
    </div>
</div>
