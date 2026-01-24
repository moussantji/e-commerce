@extends('admin.base')

@section('title', 'Modifier le produit')

@section('content')
    <div class="content">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
                <li class="breadcrumb-item active">Editer</li>
            </ol>
        </nav>
        <livewire:admin.edit-product :produit_id="$product->id" />

        @include('admin.partials.footer')
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--multiple {
                border-color: #d8dadd;
                min-height: 38px;
            }

            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #f0f2f5;
                border: 1px solid #d8dadd;
                border-radius: 4px;
                padding: 0 8px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Sélectionnez des tags',
                    allowClear: true,
                    tags: true
                });
            });
        </script>
    @endpush
@endsection
