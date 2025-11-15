@extends('admin.base')

@section('title', 'Utilisateurs')

@section('content')
    <div class="content">


        <div class="mb-9">
            <div class="row g-2 mb-4">
                <div class="col-auto">
                    <h2 class="mb-0">Tous les utilisateurs</h2>
                </div>
            </div>
            <div id="products"
                data-list='{"valueNames":["customer","email","total-orders","total-spent","city","last-seen","last-order"],"page":10,"pagination":true}'>
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="search-box">
                                <form class="position-relative">
                                    <input class="form-control search-input search"
                                           type="search" 
                                           placeholder="Rechercher..." 
                                           aria-label="Search" />
                                    <span class="fas fa-search search-box-icon"></span>
                                </form>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
                                <span class="fas fa-plus me-2"></span>Ajouter admin
                            </a>
                        </div>
                    </div>
                </div>
                @include('admin.partials.dashboard.table_client')
            </div>
        </div>

        @include('admin.partials.footer')

    </div>
@endsection
