@extends('admin.base')

@section('title', 'Ajouter un produit')

@section('content')
    <div class="content">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">Ajouter</li>
            </ol>
        </nav>
        @livewire('admin.addProduct')
        
        @include('admin.partials.footer')
    </div>


@endsection
