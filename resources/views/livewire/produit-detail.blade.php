<div>
    <div class="pt-5 pb-9">

        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="py-0">
            <div class="container-small">
                <nav class="mb-3" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a
                                href="#">{{ optional($product->category)->name ?? 'Products' }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Product' }}</li>
                    </ol>
                </nav>
                <div class="row g-5 mb-5 mb-lg-8" data-product-details="data-product-details">
                    <div class="col-12 col-lg-6">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-2 col-lg-12 col-xl-2">
                                <div class="swiper-products-thumb swiper swiper theme-slider overflow-visible"
                                    id="swiper-products-thumb"></div>
                            </div>
                            <div class="col-12 col-md-10 col-lg-12 col-xl-10">
                                <div
                                    class="d-flex align-items-center border border-translucent rounded-3 text-center p-5 h-100">
                                    <div class="swiper swiper theme-slider" data-thumb-target="swiper-products-thumb"
                                        data-products-swiper='{"slidesPerView":1,"spaceBetween":16,"thumbsEl":".swiper-products-thumb"}'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex"><button
                                class="btn btn-lg btn-outline-warning rounded-pill w-100 me-3 px-2 px-sm-4 fs-9 fs-sm-8"><span
                                    class="me-2 far fa-heart"></span>Add to wishlist</button><a
                                class="btn btn-lg btn-warning rounded-pill w-100 fs-9 fs-sm-8"
                                onclick="addToCartWithQty({{ $product->id }})"><span
                                    class="fas fa-shopping-cart me-2"></span>Add to cart</a></div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div class="d-flex flex-wrap align-items-center mb-2">
                                    <div class="me-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @php
                                                $filled = $ratingAvg >= $i;
                                                $half = !$filled && $ratingAvg >= $i - 0.5;
                                            @endphp
                                            @if ($filled)
                                                <span class="fa fa-star text-warning"></span>
                                            @elseif($half)
                                                <span class="fa fa-star-half-alt star-icon text-warning"></span>
                                            @else
                                                <span class="fa-regular fa-star text-warning-light"
                                                    data-bs-theme="light"></span>
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-primary fw-semibold mb-0">{{ number_format($ratingAvg, 1) }} / 5 •
                                        {{ $ratingCount }} avis</p>
                                </div>
                                <h3 class="mb-3 lh-sm">{{ $product->name ?? 'Produit' }}</h3>
                                <div class="d-flex flex-wrap align-items-start mb-3">
                                    @if (optional($product->brand)->name)
                                        <span
                                            class="badge text-bg-success fs-9 rounded-pill me-2 fw-semibold">{{ optional($product->brand)->name }}</span>
                                    @endif
                                    @if ($product->sku ?? false)
                                        <span class="fw-semibold">SKU: {{ $product->sku }}</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <h1 class="me-3">{{ number_format($price, 2) }} {{ config('app.currency', '') }}
                                    </h1>
                                    @if ($original > $price)
                                        <p class="text-body-quaternary text-decoration-line-through fs-6 mb-0 me-3">
                                            {{ number_format($original, 2) }} {{ config('app.currency', '') }}</p>
                                        @php $discount = $original > 0 ? round((($original - $price)/$original)*100) : 0; @endphp
                                        <p class="text-warning fw-bolder fs-6 mb-0">{{ $discount }}% off</p>
                                    @endif
                                </div>
                                @if ($inStock)
                                    <p class="text-success fw-semibold fs-7 mb-2">En stock</p>
                                @else
                                    <p class="text-danger fw-semibold fs-7 mb-2">Rupture de stock</p>
                                @endif
                                @php
                                    $desc = strip_tags($product->description ?? '');
                                    $short = mb_strlen($desc) > 160 ? mb_substr($desc, 0, 160) . '...' : $desc;
                                @endphp
                                <p class="mb-2 text-body-secondary">{!! nl2br(e($short)) !!}</p>
                            </div>
                            <div>
                                <div class="mb-3">
                                    <p class="fw-semibold mb-2 text-body">
                                        Color :
                                        <span class="text-body-emphasis" data-product-color="data-product-color">
                                        </span>
                                    </p>
                                    <div class="d-flex product-color-variants"
                                        data-product-color-variants="data-product-color-variants">
                                        <div class="rounded-1 border border-translucent me-2 active"
                                            data-variant="@foreach ($couleurs as $couleur)
                                                {{ $couleur->pivot->value }}{{ !$loop->last ? ', ' : '' }} @endforeach "
                                            data-products-images='{{ json_encode($images) }}'>
                                            <img src="{{ $images[0] }}" alt="" width="38" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row g-3 g-sm-5 align-items-end">
                                    <div class="col-12 col-sm">
                                        <p class="fw-semibold mb-2 text-body">Quantité : </p>
                                        <div class="d-flex justify-content-between align-items-end">
                                            <!-- Le FORM (invisible) -->
                                            <form wire:submit="addToCart({{ $product->id }})"
                                                id="cart-form-{{ $product->id }}" style="display: contents;">
                                                <input type="number" name="quantity" hidden value="1"
                                                    id="qty-hidden-{{ $product->id }}" />
                                            </form>

                                            <div id="qty-container-{{ $product->id }}"
                                                class="d-flex flex-between-center" data-quantity="data-quantity">

                                                <button class="btn btn-phoenix-primary px-3 js-qty-minus"
                                                    id="qty-minus-{{ $product->id }}" data-action="decrement"><span
                                                        class="fas fa-minus"></span></button>
                                                <input
                                                    class="form-control text-center input-spin-none bg-transparent border-0 outline-none qty-input"
                                                    wire:model="quantity" style="width:60px;" type="number"
                                                    min="1" value="1" disabled />
                                                <button class="btn btn-phoenix-primary px-3 js-qty-plus"
                                                    data-action="increment" id="qty-plus-{{ $product->id }}"><span
                                                        class="fas fa-plus"></span></button>
                                            </div>
                                            <button class="btn btn-phoenix-primary px-3 border-0"><span
                                                    class="fas fa-share-alt fs-7"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================-->
        <!-- ============================================-->



        <!-- ============================================-->
        <!-- <section> begin ============================-->
        <section class="py-0">
            <div class="container-small">
                <ul class="nav nav-underline fs-9 mb-4" id="productTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            href="#tab-description" role="tab" aria-controls="tab-description"
                            aria-selected="true">Description</a></li>
                    <li class="nav-item"><a class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                            href="#tab-reviews" role="tab" aria-controls="tab-reviews"
                            aria-selected="false">Avis &amp; notes
                            ({{ $ratingCount }})</a></li>
                </ul>
                <div class="row gx-3 gy-7">
                    <div class="col-12 col-lg-7 col-xl-8">
                        <div class="tab-content" id="productTabContent">
                            <div class="tab-pane pe-lg-6 pe-xl-12 fade show active text-body-emphasis"
                                id="tab-description" role="tabpanel" aria-labelledby="description-tab">
                                <p class="mb-5">{!! $product->description ?? 'Aucune description.' !!}</p>
                                @php $descImage = optional($product->getPhoto())->getImageUrl(530,530); @endphp
                                @if ($descImage || !empty($images))
                                    <a href="{{ $descImage ?? $images[0] }}" data-gallery="gallery-description"><img
                                            class="img-fluid mb-5 rounded-3" src="{{ $descImage ?? $images[0] }}"
                                            alt=""></a>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="tab-reviews" role="tabpanel"
                                aria-labelledby="reviews-tab">
                                <div class="bg-body-emphasis rounded-3 p-4 border border-translucent">
                                    <div class="row g-3 justify-content-between mb-4">
                                        <div class="col-auto">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <h2 class="fw-bolder me-3">{{ number_format($ratingAvg, 1) }}<span
                                                        class="fs-8 text-body-quaternary fw-bold">/5</span></h2>
                                                <div class="me-3">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $filled = $ratingAvg >= $i;
                                                            $half = !$filled && $ratingAvg >= $i - 0.5;
                                                        @endphp
                                                        @if ($filled)
                                                            <span class="fa fa-star text-warning fs-6"></span>
                                                        @elseif($half)
                                                            <span
                                                                class="fa fa-star-half-alt star-icon text-warning fs-6"></span>
                                                        @else
                                                            <span class="fa-regular fa-star text-warning-light fs-6"
                                                                data-bs-theme="light"></span>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <p class="text-body mb-0 fw-semibold fs-7">{{ $ratingCount }} notes
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @php $reviews = method_exists($product,'avisClients') ? $product->avisClients()->latest('id')->limit(3)->get() : collect(); @endphp
                                    @forelse($reviews as $review)
                                        <div class="mb-4 hover-actions-trigger btn-reveal-trigger">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="mb-2">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span
                                                            class="fa {{ $i <= (int) ($review->nb_etoiles ?? 0) ? 'fa-star' : 'fa-regular fa-star' }} text-warning"></span>
                                                    @endfor
                                                    <span class="text-body-secondary ms-1">par</span>
                                                    {{ optional($review->user)->name ?? 'Client' }}
                                                </h5>
                                            </div>
                                            <p class="text-body-tertiary fs-9 mb-1">
                                                {{ optional($review->created_at)->diffForHumans() }}</p>
                                            <p class="text-body-highlight mb-1">{{ $review->commentaire ?? '' }}</p>
                                        </div>
                                    @empty
                                        <p class="text-body-tertiary mb-0">Aucun avis pour le moment.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-body-emphasis">Souvent achetés ensemble</h5>
                                <div class="w-75">
                                    <p class="text-body-tertiary fs-9 fw-bold line-clamp-1">avec
                                        {{ $product->name ?? 'Produit' }}</p>
                                </div>
                                <div class="border-dashed border-y border-translucent py-4">
                                    @foreach ($bundleItems as $item)
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="form-check mb-0"><input class="form-check-input"
                                                    type="checkbox" checked="checked" /><label
                                                    class="form-check-label"></label></div>
                                            <a href="#"> <img class="border border-translucent rounded"
                                                    src="{{ $item['img'] }}" width="53" alt="" /></a>
                                            <div class="ms-2">
                                                <a class="fs-9 fw-bold line-clamp-2 mb-2"
                                                    href="#">{{ $item['name'] }}</a>
                                                <h5>{{ number_format($item['price'], 2) }}
                                                    {{ config('app.currency', '') }}</h5>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @php
                                    $bundleTotal = collect($bundleItems)->sum('price');
                                @endphp
                                <div class="d-flex align-items-end justify-content-between pt-3">
                                    <div>
                                        <h5 class="mb-2 text-body-tertiary text-opacity-85">Total</h5>
                                        <h4 class="mb-0 text-body-emphasis">{{ number_format($bundleTotal, 2) }}
                                            {{ config('app.currency', '') }}</h4>
                                    </div>
                                    <div class="btn btn-outline-warning">Ajouter {{ count($bundleItems) }} articles au
                                        panier<span class="fas fa-shopping-cart ms-2"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end of .container-->
        </section><!-- <section> close ============================-->
        <!-- ============================================-->

    </div>

    <!-- ============================================-->
    <!-- <section> begin ============================-->
    <section class="py-0 mb-9">
        <div class="container">
            <div class="d-flex flex-between-center mb-3">
                <div>
                    <h3>Produits similaires</h3>
                    <p class="mb-0 text-body-tertiary fw-semibold">Essentiels pour une meilleure vie</p>
                </div>
            </div>
            <div class="swiper-theme-container products-slider">
                <div class="swiper swiper theme-slider"
                    data-swiper='{"slidesPerView":1,"spaceBetween":16,"breakpoints":{"450":{"slidesPerView":2,"spaceBetween":16},"768":{"slidesPerView":3,"spaceBetween":16},"992":{"slidesPerView":4,"spaceBetween":16},"1200":{"slidesPerView":5,"spaceBetween":16},"1540":{"slidesPerView":6,"spaceBetween":16}}}'>
                    <div class="swiper-wrapper">
                        @forelse($similarProducts as $p)
                            <div class="swiper-slide">
                                <div class="position-relative text-decoration-none product-card h-100">
                                    <div class="d-flex flex-column justify-content-between h-100">
                                        <div>
                                            <div
                                                class="border border-1 border-translucent rounded-3 position-relative mb-3">
                                                <button class="btn btn-wish btn-wish-primary z-2 d-toggle-container"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Ajouter aux favoris"><span
                                                        class="fas fa-heart d-block-hover"
                                                        data-fa-transform="down-1"></span><span
                                                        class="far fa-heart d-none-hover"
                                                        data-fa-transform="down-1"></span></button>
                                                <img class="img-fluid" src="{{ $p['img'] }}" alt="" />
                                            </div>
                                            <a class="stretched-link" href="#">
                                                <h6 class="mb-2 lh-sm line-clamp-3 product-name">{{ $p['name'] }}
                                                </h6>
                                            </a>
                                        </div>
                                        <div>
                                            @if (($p['original'] ?? 0) > ($p['price'] ?? 0))
                                                <div class="d-flex align-items-center mb-1">
                                                    <p class="me-2 text-body text-decoration-line-through mb-0">
                                                        {{ number_format($p['original'], 2) }}
                                                        {{ config('app.currency', '') }}</p>
                                                    <h3 class="text-body-emphasis mb-0">
                                                        {{ number_format($p['price'], 2) }}
                                                        {{ config('app.currency', '') }}</h3>
                                                </div>
                                            @else
                                                <h3 class="text-body-emphasis mb-0">
                                                    {{ number_format($p['price'], 2) }}
                                                    {{ config('app.currency', '') }}</h3>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <p class="text-body-tertiary">Aucun produit similaire.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="swiper-nav">
                    <div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span></div>
                    <div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>
                </div>
            </div>
        </div><!-- end of .container-->
    </section>
    <!-- <section> close ============================-->
    <!-- ============================================-->

    <script>
        function addToCartWithQty(productId) {
            // Récupère la vraie quantité du DOM
            const qtyInput = document.querySelector(`#qty-container-${productId} .qty-input`);
            const quantity = parseInt(qtyInput.value) || 1;

            // ENVOIE À LIVEWIRE ✅
            @this.call('addToCart', productId, quantity);
        }
    </script>

</div>
