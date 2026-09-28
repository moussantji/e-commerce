@extends('admin.base')

@section('title', 'Modifier la catégorie')

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
            <span class="here">{{ $category->name }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Modifier « {{ $category->name }} »</h1>
            <p>
                {!! $category->is_active ? '<span class="st ok">Active</span>' : '<span class="st ko">Inactive</span>' !!}
                @if ($category->parent)
                    <span class="muted-sm">Sous-catégorie de {{ $category->parent->name }}</span>
                @endif
            </p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST"
                    enctype="multipart/form-data">
                    @include('admin.categories._form', [
                        'action' => route('admin.categories.update', $category),
                        'method' => 'PUT',
                        'category' => $category,
                        'categories' => $categories,
                        'submitLabel' => 'Mettre à jour',
                    ])
                </form>
            </div>
        </div>
    </section>
@endsection
