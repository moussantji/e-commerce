@extends('base')

@section('title', 'Connexion')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Connexion</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mon espace client</h1>
            <p>Connectez-vous pour suivre vos commandes, retrouver vos favoris et vos informations de livraison.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-user" />
                        </svg> Connexion</h2>
                    <form method="POST" action="{{ route('login') }}" style="margin-top:14px">
                        @csrf
                        <div class="field @error('email') bad @enderror">
                            <label for="email">Adresse email</label>
                            <input class="ctrl" id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="vous@email.com" required autofocus autocomplete="username">
                            @error('email') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field @error('password') bad @enderror">
                            <label for="password">Mot de passe</label>
                            <input class="ctrl" id="password" type="password" name="password" placeholder="••••••••"
                                required autocomplete="current-password">
                            @error('password') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:16px">
                            <label class="switch"><input type="checkbox" name="remember"
                                    {{ old('remember') ? 'checked' : '' }}> Se souvenir de moi</label>
                            @if (Route::has('password.request'))
                                <a class="lien" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                            @endif
                        </div>
                        <div class="pdp-actions">
                            <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                                    <use href="#i-user" />
                                </svg> Se connecter</button>
                        </div>
                    </form>
                    <div
                        style="display:flex;align-items:center;gap:12px;margin:18px 0 14px;font-size:12px;color:var(--grey)">
                        <span style="flex:1;height:1px;background:var(--line)"></span> ou <span
                            style="flex:1;height:1px;background:var(--line)"></span>
                    </div>
                    <div class="pdp-actions">
                        <a class="btn-line" style="flex:1" href="{{ url('/auth/google/redirect') }}"><span
                                style="font-weight:800;background:linear-gradient(135deg,#4285F4,#EA4335,#FBBC05,#34A853);-webkit-background-clip:text;background-clip:text;color:transparent">G</span>
                            Continuer avec Google</a>
                    </div>
                    <p class="muted-sm" style="text-align:center;margin-top:16px">Pas encore de compte ?
                        <a class="lien" href="{{ route('register') }}">Créer un compte</a>
                    </p>
                </div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-info" />
                        </svg> À quoi ça sert ?</h2>
                    <ul class="liste-check">
                        <li>Suivre l'avancement de chaque commande (confirmée → payée → expédiée → livrée)</li>
                        <li>Retrouver vos favoris enregistrés sur votre compte</li>
                        <li>Voir le détail de vos achats et vos points de fidélité</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
