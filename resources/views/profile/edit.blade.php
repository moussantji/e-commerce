@extends('base')

@section('title', 'Mon profil')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Mon profil</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mon profil</h1>
            <p>Informations personnelles, mot de passe et paramètres du compte.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            @include('profile.partials.boutique', [
                'pfUser' => $user,
                'updateRoute' => 'profile.update',
                'passwordRoute' => 'profile.password',
                'deleteRoute' => 'profile.destroy',
                'backRoute' => 'dashboard',
                'backLabel' => 'Mes commandes',
                'roleLabel' => 'client',
                'emailLocked' => true,
            ])
        </div>
    </section>

    @include('partials.footer')
@endsection
