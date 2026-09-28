@extends('base')

@section('title', 'Créer un compte')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Créer un compte</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Créer un compte</h1>
            <p>Commandez plus vite, suivez vos colis et cumulez des points de fidélité.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-user" />
                        </svg> Inscription</h2>
                    <form method="POST" action="{{ route('register') }}" style="margin-top:14px">
                        @csrf
                        <div class="field @error('name') bad @enderror">
                            <label for="name">Nom complet</label>
                            <input class="ctrl" id="name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="Ex. Ibrahim Sangaré" required autofocus autocomplete="name">
                            @error('name') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field @error('email') bad @enderror">
                            <label for="email">Adresse email</label>
                            <input class="ctrl" id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="vous@email.com" required autocomplete="username">
                            @error('email') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field @error('password') bad @enderror">
                            <label for="password">Mot de passe</label>
                            <input class="ctrl" id="password" type="password" name="password" placeholder="••••••••"
                                required autocomplete="new-password">
                            @error('password') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <input class="ctrl" id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="••••••••" required autocomplete="new-password">
                        </div>
                        <label class="switch" style="margin-bottom:16px"><input type="checkbox" name="terms" required>
                            <span>J'accepte les <a class="lien" href="{{ route('conditions') }}">conditions
                                    d'utilisation</a></span></label>
                        <div class="pdp-actions">
                            <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                                    <use href="#i-b2-check" />
                                </svg> Créer mon compte</button>
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
                    <p class="muted-sm" style="text-align:center;margin-top:16px">Déjà inscrit ?
                        <a class="lien" href="{{ route('login') }}">Se connecter</a>
                    </p>
                </div>
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-bag" />
                        </svg> Vos avantages</h2>
                    <ul class="liste-check">
                        <li>Livraison offerte dès 25 000 FCFA d'achat</li>
                        <li>Paiement Mobile Money : Orange, Moov, Wave</li>
                        <li>15 points de fidélité par commande</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
