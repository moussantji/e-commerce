@extends('base')

@section('title', 'Mot de passe oublié')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('login') }}">Connexion</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Mot de passe oublié</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mot de passe oublié</h1>
            <p>Recevez par email un lien pour réinitialiser votre mot de passe.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-lock" />
                        </svg> Réinitialisation</h2>
                    @if (session('status'))
                        <div class="tagline-band" style="margin:14px 0 0">
                            <svg class="ic">
                                <use href="#i-b2-check" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}" style="margin-top:14px">
                        @csrf
                        <div class="field @error('email') bad @enderror">
                            <label for="email">Adresse email du compte</label>
                            <input class="ctrl" id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="vous@email.com" required autofocus>
                            @error('email') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="pdp-actions">
                            <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                                    <use href="#i-card" />
                                </svg> Recevoir le lien</button>
                        </div>
                    </form>
                    <p class="muted-sm" style="text-align:center;margin-top:16px">
                        <a class="lien" href="{{ route('login') }}">Retour à la connexion</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
