@extends('admin.base')

@section('title', "Modifier l'utilisateur")

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
            <span class="here">{{ $user->name }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Modifier {{ $user->name }}</h1>
            <p>
                {!! ($user->status ?? '') === 'active' ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}
                <span class="muted-sm">{{ ucfirst($user->role) }} · {{ $user->email }}</span>
            </p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @include('admin.users._form', ['user' => $user])
                </form>
            </div>
        </div>
    </section>
@endsection
