@extends('admin.base')

@section('title', 'Créer un tag')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.tags.index') }}">Tags</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Nouveau</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Nouveau tag</h1>
            <p>Créez une étiquette pour le catalogue.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                @include('admin.tags.form', ['tag' => null])
            </div>
        </div>
    </section>
@endsection
