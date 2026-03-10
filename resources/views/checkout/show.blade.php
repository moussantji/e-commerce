@extends('base')

@section('title', 'Checkout - Finaliser commande')

@section('content')

    <!-- ============================================-->
    <!-- <section> begin ============================-->
    @include('section-begin')
    <!-- <section> close ============================-->
    <!-- ============================================-->

    @include('partials.nav')

    <!-- ============================================-->
    <section class="pt-5 pb-9">
        <div class="container-small">
            <nav class="mb-3" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('panier') }}">Panier</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
            <h2 class="mb-5">Check out</h2>

            <form method="POST" action="#">
                @csrf

                <div class="row justify-content-between">
                    <div class="col-lg-7 col-xl-7">

                        {{-- Shipping Details - VOTRE CODE EXACT --}}
                        <div class="d-flex align-items-end">
                            <h3 class="mb-0 me-3">Shipping Details</h3>
                            <a href="{{ route('profile.edit') }}" class="btn btn-link p-0">Edit</a>
                        </div>
                        <table class="table table-borderless mt-4">
                            <tbody>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            {{-- ✅ VOTRE ICÔNE 16px --}}
                                            <span class="fs-3 me-2" data-feather="user" style="height:16px; width:16px;">
                                            </span>
                                            <h5 class="lh-sm me-4 mb-0">Name</h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">:</td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">{{ auth()->user()->name }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            {{-- ✅ VOTRE ICÔNE 16px --}}
                                            <span class="fs-3 me-2" data-feather="home" style="height:16px; width:16px;">
                                            </span>
                                            <h5 class="lh-sm me-4 mb-0">Address</h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">:</td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-lg fw-normal text-body-secondary">
                                            {{ auth()->user()->adresse ?? 'Ajouter adresse' }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            {{-- ✅ VOTRE ICÔNE 16px --}}
                                            <span class="fs-3 me-2" data-feather="phone" style="height:16px; width:16px;">
                                            </span>
                                            <h5 class="lh-sm me-4 mb-0">Phone</h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">:</td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">{{ auth()->user()->telephone }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr class="my-6">

                        {{-- Billing Details - VOTRE CODE EXACT --}}
                        <h3>Billing Details</h3>
                        <div class="form-check">
                            <input class="form-check-input" id="sameAsShipping" type="checkbox" checked disabled />
                            <label class="form-check-label fs-8 fw-normal" for="sameAsShipping">Same as shipping
                                address</label>
                        </div>
                        <table class="table table-borderless mt-4">
                            <tbody>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            {{-- ✅ VOTRE ICÔNE 16px --}}
                                            <span class="fs-3 me-2" data-feather="user" style="height:16px; width:16px;">
                                            </span>
                                            <h5 class="lh-sm me-4 mb-0">Name</h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">:</td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">{{ auth()->user()->name }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            {{-- ✅ VOTRE ICÔNE 16px --}}
                                            <span class="fs-3 me-2" data-feather="home" style="height:16px; width:16px;">
                                            </span>
                                            <h5 class="lh-sm me-4 mb-0">Address</h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">:</td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-lg fw-normal text-body-secondary">
                                            {{ auth()->user()->adresse ?? 'Ajouter adresse' }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 ps-0">
                                        <div class="d-flex">
                                            {{-- ✅ VOTRE ICÔNE 16px --}}
                                            <span class="fs-3 me-2" data-feather="phone" style="height:16px; width:16px;">
                                            </span>
                                            <h5 class="lh-sm me-4 mb-0">Phone</h5>
                                        </div>
                                    </td>
                                    <td class="py-2 fw-bold lh-sm">:</td>
                                    <td class="py-2 px-3">
                                        <h5 class="lh-sm fw-normal text-body-secondary">{{ auth()->user()->telephone }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <hr class="my-6">

                        {{-- Delivery Type - VOTRE STRUCTURE --}}
                        <h3 class="mb-5">Delivery Type</h3>
                        @php $livraisons = \App\Models\Livraison::where('is_active', true)->get(); @endphp
                        <div class="row gy-6">
                            @foreach ($livraisons as $livraison)
                                <div class="col-12 col-md-6">
                                    <div class="d-flex flex-wrap align-items-center mb-3">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="delivery_method_id"
                                                id="delivery_{{ $livraison->id }}" value="{{ $livraison->id }}"
                                                {{ $delivery_method_id == $livraison->id ? 'checked' : '' }}>
                                            <label class="form-check-label fs-8 text-body"
                                                for="delivery_{{ $livraison->id }}">
                                                {{ $livraison->name }}
                                            </label>
                                        </div>
                                        <span class="d-inline-block text-body-emphasis fw-bold ms-2">
                                            {{ number_format($livraison->price, 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                    <div class="ps-4">
                                        <h6 class="text-body-tertiary mb-2">Est. delivery: 3-7 jours</h6>
                                        <h6 class="text-info lh-base mb-0">{{ $livraison->description }}</h6>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr class="my-6">

                        {{-- Payment Method - VOTRE CODE EXACT --}}
                        <h3 class="mb-5">Payment Method</h3>
                        <div class="row g-4 mb-7">
                            <div class="col-12">
                                <div class="row gx-lg-11">
                                    @foreach ($paymentMethods as $method)
                                        <div class="col-md-auto">
                                            <div class="form-check">
                                                <input class="form-check-input" id="payment_{{ $method->id }}"
                                                    type="radio" name="payment_method_id"
                                                    value="{{ $method->id }}" />
                                                <label class="form-check-label fs-8 text-body"
                                                    for="payment_{{ $method->id }}">
                                                    {{ $method->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Buttons - VOTRE CODE EXACT --}}
                        <div class="row g-2 mb-5 mb-lg-0">
                            <div class="col-md-8 col-lg-9 d-grid">
                                <button class="btn btn-primary w-100 py-3" type="submit">
                                    Pay {{ number_format($subtotal + 15000, 0, ',', ' ') }} FCFA
                                </button>
                            </div>
                            <div class="col-md-4 col-lg-3 d-grid">
                                <a href="{{ route('panier') }}"
                                    class="btn btn-outline-secondary w-100 py-3 text-nowrap">Edit cart</a>
                            </div>
                        </div>
                    </div>

                    {{-- Summary - VOTRE CODE EXACT --}}
                    <div class="col-lg-5 col-xl-4">
                        <div class="card mt-3 mt-lg-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h3 class="mb-0">Summary</h3>
                                    <a href="{{ route('panier') }}" class="btn btn-link pe-0">Edit cart</a>
                                </div>

                                <div class="border-dashed border-bottom border-translucent mt-4">
                                    <div class="ms-n2">
                                        @forelse($panierItems as $item)
                                            <div class="row align-items-center mb-2 g-3">
                                                <div class="col-8 col-md-7 col-lg-8">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded me-2 ms-1 d-flex align-items-center justify-content-center"
                                                            style="width:40px;height:40px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                        <h6 class="fw-semibold text-body-highlight lh-base mb-0">
                                                            {{ Str::limit($item->name, 45) }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <div class="col-2 col-md-3 col-lg-2">
                                                    <h6 class="fs-10 mb-0">x{{ $item->pivot->quantite }}</h6>
                                                </div>
                                                <div class="col-2 ps-0">
                                                    <h5 class="mb-0 fw-semibold text-end">
                                                        {{ number_format($item->pivot->total_ligne, 0, ',', ' ') }} FCFA
                                                    </h5>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-muted">Panier vide</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="border-dashed border-bottom border-translucent mt-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h5 class="text-body fw-semibold">Items subtotal:</h5>
                                        <h5 class="text-body fw-semibold">{{ number_format($subtotal, 0, ',', ' ') }} FCFA
                                        </h5>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="text-body fw-semibold">Shipping Cost</h5>
                                        <h5 class="text-body fw-semibold">15 000 FCFA</h5>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between border-dashed-y pt-3">
                                    <h4 class="mb-0">Total :</h4>
                                    <h4 class="mb-0">{{ number_format($subtotal + 15000, 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- ============================================-->

@endsection
