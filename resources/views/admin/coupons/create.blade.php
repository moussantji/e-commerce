@extends('admin.base')

@section('title', 'Nouveau coupon')

@section('content')
    <nav class="crumb" aria-label="Fil d'Ariane">
        <div class="wrap">
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <a href="{{ route('admin.coupons.index') }}">Coupons</a>
            <svg class="ic">
                <use href="#i-chevron" />
            </svg>
            <span class="here">Nouveau</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Nouveau coupon</h1>
            <p>Créez un code promo pour le panier.</p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @include('admin.coupons._form', ['coupon' => null])
                </form>
            </div>
        </div>
    </section>
@endsection
