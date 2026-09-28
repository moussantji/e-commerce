@extends('base')

@section('title', 'Confirmer le mot de passe')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Sécurité</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Zone sécurisée</h1>
            <p>Confirmez votre mot de passe pour continuer.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-lock" />
                        </svg> Confirmation</h2>
                    <form method="POST" action="{{ route('password.confirm') }}" style="margin-top:14px">
                        @csrf
                        <div class="field @error('password') bad @enderror">
                            <label for="password">Mot de passe</label>
                            <input class="ctrl" id="password" type="password" name="password" placeholder="••••••••"
                                required autocomplete="current-password">
                            @error('password') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="pdp-actions">
                            <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                                    <use href="#i-b2-check" />
                                </svg> Confirmer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
