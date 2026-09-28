@extends('base')

@section('title', 'Nouveau mot de passe')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Nouveau mot de passe</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Nouveau mot de passe</h1>
            <p>Choisissez un nouveau mot de passe pour votre compte.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="dash-narrow">
                <div class="panel">
                    <h2><svg class="ic">
                            <use href="#i-b2-lock" />
                        </svg> Réinitialisation</h2>
                    <form method="POST" action="{{ route('password.store') }}" style="margin-top:14px">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="field @error('email') bad @enderror">
                            <label for="email">Adresse email</label>
                            <input class="ctrl" id="email" type="email" name="email"
                                value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                            @error('email') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field @error('password') bad @enderror">
                            <label for="password">Nouveau mot de passe</label>
                            <input class="ctrl" id="password" type="password" name="password" placeholder="••••••••"
                                required autocomplete="new-password">
                            @error('password') <span class="avis-err">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <input class="ctrl" id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="••••••••" required autocomplete="new-password">
                        </div>
                        <div class="pdp-actions">
                            <button class="btn-solid" style="flex:1" type="submit"><svg class="ic">
                                    <use href="#i-b2-check" />
                                </svg> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
