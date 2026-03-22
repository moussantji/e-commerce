@extends('admin.base')

@section('title', 'Tableau de bord')

@section('content')
    <div class="content">
        @include('admin.partials.dashboard.header')
        
        <div class="row g-3 mb-3">
            <div class="col-12">
                @include('admin.partials.dashboard.sales-chart')
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                @include('admin.partials.dashboard.table')
            </div>
        </div>
        
        @include('admin.partials.footer')
    </div>

    @stack('scripts')
@endsection
