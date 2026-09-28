@extends('base')

@section('title', 'Mon profil — Administration')

@section('content')
    @include('section-begin')

    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('home') }}">Accueil</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Mon profil</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Mon profil administrateur</h1>
            <p>Informations personnelles, mot de passe et paramètres du compte.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            @include('profile.partials.boutique', [
                'pfUser' => $user,
                'updateRoute' => 'admin.profile.update',
                'passwordRoute' => 'admin.profile.password',
                'deleteRoute' => 'admin.profile.destroy',
                'backRoute' => 'admin.dashboard',
                'backLabel' => 'Retour admin',
                'roleLabel' => 'administrateur',
            ])
        </div>
    </section>

    @include('partials.footer')
@endsection
