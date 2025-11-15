@extends('admin.base')

@section('title', 'Gestion des commandes')

@section('content')
    <div class="content">
        <div class="mb-9">
            <div class="row g-3 mb-4">
                <div class="col-auto">
                    <h2 class="mb-0">Orders</h2>
                </div>
            </div>
            <div id="orderTable"
                data-list='{"valueNames":["order","total","customer","payment_status","fulfilment_status","delivery_type","date"],"page":10,"pagination":true}'>
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="search-box">
                                <form id="filterForm" action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2">
                                    <div class="search-box">
                                        <input class="form-control search-input search"
                                            type="search" 
                                            id="searchInput" 
                                            placeholder="Rechercher..." 
                                            value="{{ request('search') }}" 
                                            aria-label="Search" 
                                            onkeyup="updateSearchValue(this)" />
                                        <span class="fas fa-search search-box-icon"></span>
                                    </div>
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">Tous les statuts</option>
                                        @foreach($statuses as $value => $label)
                                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="searchValue" value="{{ request('search') }}">
                                </form>
                                <script>
                                    function updateSearchValue(input) {
                                        document.getElementById('searchValue').value = input.value.trim();
                                    }
                                    
                                    // Soumettre le formulaire lors de la touche Entrée
                                    document.getElementById('searchInput').addEventListener('keypress', function(e) {
                                        if (e.key === 'Enter') {
                                            e.preventDefault();
                                            document.getElementById('filterForm').submit();
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                    <div class="table-responsive scrollbar mx-n1 px-1">
                        @include('admin.partials.dashboard.table_commande')
                    </div>
                    <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                        <div class="col-auto d-flex">
                            <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info">
                            </p>
                            <a class="fw-semibold" href="#!" data-list-view="*">View all<span
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
@endsection
