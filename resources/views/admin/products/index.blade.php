@extends('admin.base')

@section('title', 'Liste des produits')

@section('content')
    <div class="content">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#!">Tableau de bord</a></li>
                <li class="breadcrumb-item active">Produits</li>
            </ol>
        </nav>
        <div class="mb-9">
            <div class="row g-3 mb-4">
                <div class="col-auto">
                    <h2 class="mb-0">Produits</h2>
                </div>
            </div>
            <div id="products"
                data-list='{"valueNames":["product","price","category","tags","vendor","time"],"page":10,"pagination":true}'>
                <div class="mb-4">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="search-box">
                            <form action="{{ route('admin.products.index') }}" method="GET" class="position-relative">
                                <input type="text" name="search" class="form-control search-input search"
                                    placeholder="Rechercher des produits..." aria-label="Search"
                                    value="{{ request('search') }}">

                            </form>
                        </div>
                        <div class="scrollbar overflow-hidden-y">
                            <div class="btn-group position-static" role="group">
                                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex gap-2">
                                    @if (request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif

                                    <div class="btn-group position-static text-nowrap">
                                        <select name="category" class="form-select" onchange="this.form.submit()">
                                            <option value="">Toutes les catégories</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="btn-group position-static text-nowrap">
                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                            <option value="">Tous les statuts</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Actif</option>
                                            <option value="inactive"
                                                {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                        </select>
                                    </div>

                                    @if (request('search') || request('category') || request('status'))
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i> R.A.Z
                                        </a>
                                    @endif
                                </form>
                            </div>
                        </div>
                        <div class="ms-xxl-auto">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary" id="addBtn">
                                <span class="fas fa-plus me-2"></span>Ajouter un produit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card shadow-none border-0">
                    <div class="table-responsive">
                        @include('admin.partials.table_produit')
                    </div>

                    <style>
                        .table> :not(caption)>*>* {
                            padding-top: 1rem;
                            padding-bottom: 1rem;
                            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                        }

                        .table> :not(:first-child) {
                            border-top: none;
                        }

                        .form-switch .form-check-input {
                            width: 2.5em;
                            margin-left: -2.5em;
                            background-color: #e9ecef;
                            border: 1px solid #dee2e6;
                        }

                        .form-switch .form-check-input:checked {
                            background-color: #0d6efd;
                            border-color: #0d6efd;
                        }

                        .btn-icon {
                            width: 32px;
                            height: 32px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            border-radius: 8px;
                        }
                    </style>

                    <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                        <div class="col-auto d-flex">
                            <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info">
                            </p><a class="fw-semibold" href="#!" data-list-view="*">View all<span
                                    class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a
                                class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span
                                    class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                        </div>
                        <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span
                                    class="fas fa-chevron-left"></span></button>
                            <ul class="mb-0 pagination"></ul><button class="page-link pe-0"
                                data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    </div>
@endsection
