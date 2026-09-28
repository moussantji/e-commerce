@extends('admin.base')

@section('title', 'Nouvelle catégorie')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.categories.index') }}">Catégories</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Nouvelle</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Nouvelle catégorie</h1>
            <p>Créez un rayon pour organiser le catalogue.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @include('admin.categories._form', [
                        'action' => route('admin.categories.store'),
                        'method' => 'POST',
                        'category' => null,
                        'categories' => $categories,
                        'submitLabel' => 'Créer la catégorie',
                    ])
                </form>
            </div>
        </div>
    </section>
@endsection
