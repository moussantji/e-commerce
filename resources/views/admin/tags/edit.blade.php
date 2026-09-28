@extends('admin.base')

@section('title', 'Modifier le tag')

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
            <span class="here">{{ $tag->name }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Modifier « {{ $tag->name }} »</h1>
            <p>Mettez à jour l'étiquette.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                @include('admin.tags.form', ['tag' => $tag])
            </div>
        </div>
    </section>
@endsection
