@extends('admin.base')

@section('title', 'Nouvel utilisateur')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.users.index') }}">Clients</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Nouveau</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Nouvel utilisateur</h1>
            <p>Créez un compte client ou admin.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @include('admin.users._form', ['user' => null])
                </form>
            </div>
        </div>
    </section>
@endsection
