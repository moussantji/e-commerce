@extends('admin.base')

@section('title', 'Modifier le coupon')

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
            <span class="here">{{ $coupon->code }}</span>
        </div>
    </nav>

    <section class="phead">
        <div class="wrap">
            <h1>Modifier « {{ $coupon->code }} »</h1>
            <p>
                {!! $coupon->is_active ? '<span class="st ok">Actif</span>' : '<span class="st ko">Inactif</span>' !!}
                <span class="muted-sm">{{ $coupon->usage_count }} utilisation(s)</span>
            </p>
        </div>
    </section>

    <section>
        <div class="wrap">
            <div class="panel">
                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                    @include('admin.coupons._form', ['coupon' => $coupon])
                </form>
            </div>
        </div>
    </section>
@endsection
