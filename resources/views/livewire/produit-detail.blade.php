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
                                    id="swiper-products-thumb" wire:ignore></div>
                            </div>
                            <div class="col-12 col-md-10 col-lg-12 col-xl-10">
                                <div
                                    class="d-flex align-items-center border border-translucent rounded-3 text-center h-100">
                                    <div class="swiper swiper theme-slider" data-thumb-target="swiper-products-thumb"
                                        data-products-swiper='{"slidesPerView":1,"spaceBetween":16,"thumbsEl":".swiper-products-thumb"}'
                                        wire:ignore>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            @if (auth()->check())
                                @if ($iswishlisted)
                                    <button wire:click="toggleWishlist({{ $product->id }})"
                                        class="btn btn-lg btn-outline-warning rounded-pill w-100 me-3 px-2 px-sm-4 fs-9 fs-sm-8">

                                        <span class="fas fa-heart text-danger"></span>Remove from wishlist

                                    </button>
                                @else
                                    <button wire:click="toggleWishlist({{ $product->id }})"
                                        class="btn btn-lg btn-outline-warning rounded-pill w-100 me-3 px-2 px-sm-4 fs-9 fs-sm-8"><span
                                            class="me-2 far fa-heart"></span>Add to wishlist

                                    </button>
                                @endif
                                <a class="btn btn-lg btn-warning rounded-pill w-100 fs-9 fs-sm-8"
                                    onclick="addToCartWithQty({{ $product->id }})"><span
                                        class="fas fa-shopping-cart me-2"></span>Add to cart</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="btn btn-lg btn-outline-warning rounded-pill w-100 me-3 px-2 px-sm-4 fs-9 fs-sm-8"><span
                                        class="me-2 far fa-heart"></span>Add to wishlist</a>
                                <a class="btn btn-lg btn-warning rounded-pill w-100 fs-9 fs-sm-8"
                                    href="{{ route('login') }}"><span class="fas fa-shopping-cart me-2"></span>Add to
                                    cart</a>
                            @endif
                        </div>
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
                                        {{ $ratingCount }} avis
                                    </p>
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
                                            {{ number_format($original, 2) }} {{ config('app.currency', '') }}
                                        </p>
                                        @php $discount = $original > 0 ? round((($original - $price) / $original) * 100) : 0; @endphp
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
                                    <p class="fw-semibold mb-2 text-body" wire:ignore>
                                        Color :
                                        <span class="text-body-emphasis" data-product-color="data-product-color">
                                        </span>
                                    </p>
                                    <div class="d-flex product-color-variants"
                                        data-product-color-variants="data-product-color-variants">
                                        <div class="rounded-1 border border-translucent me-2 {{ $alwaysActive ? 'active' : '' }}"
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
                                            <button id="shareBtn" class="btn btn-phoenix-primary px-3 border-0"
                                                title="Partager">
                                                <i class="fas fa-share-alt fs-7"></i>
                                                <span class="share-status d-none ms-1">✓</span>
                                            </button>
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
                    <li class="nav-item"><a class="nav-link {{ $tab === 'description' ? 'active' : '' }}"
                            id="description-tab" data-bs-toggle="tab" href="#tab-description" role="tab"
                            aria-controls="tab-description" aria-selected="true"
                            wire:click="$set('tab', 'description')">Description</a></li>
                    <li class="nav-item"><a class="nav-link {{ $tab === 'specification' ? 'active' : '' }}"
                            id="specification-tab" data-bs-toggle="tab" href="#tab-specification" role="tab"
                            aria-controls="tab-specification" aria-selected="false"
                            wire:click="$set('tab', 'specification')">Specification</a></li>
                    <li class="nav-item"><a class="nav-link {{ $tab === 'reviews' ? 'active' : '' }}"
                            id="reviews-tab" data-bs-toggle="tab" href="#tab-reviews" role="tab"
                            aria-controls="tab-reviews" aria-selected="false"
                            wire:click="$set('tab', 'reviews')">Avis &amp; notes
                            ({{ $ratingCount }})</a></li>
                </ul>
                <div class="row gx-3 gy-7">
                    <div class="col-12 col-lg-7 col-xl-8">
                        <div class="tab-content" id="productTabContent">
                            <div class="tab-pane pe-lg-6 pe-xl-12 fade {{ $tab === 'description' ? 'show active' : '' }} text-body-emphasis"
                                id="tab-description" role="tabpanel" aria-labelledby="description-tab">
                                <p class="mb-5">{!! $product->description ?? 'Aucune description.' !!}</p>
                                @php $descImage = optional($product->getPhoto())->getImageUrl(530, 530); @endphp
                                @if ($descImage || !empty($images))
                                    <a href="{{ $descImage ?? $images[0] }}" data-gallery="gallery-description"><img
                                            class="img-fluid mb-5 rounded-3" src="{{ $descImage ?? $images[0] }}"
                                            alt=""></a>
                                @endif
                            </div>
                            <div class="tab-pane pe-lg-6 {{ $tab === 'specification' ? 'show active' : '' }} pe-xl-12 fade"
                                id="tab-specification" role="tabpanel" aria-labelledby="specification-tab">

                                <!-- Ici : autres blocs éventuels (Processor, Storage, etc.) si tu en veux -->

                                <h3 class="mb-0 mt-6 ms-4 fw-bold">Additional Specifications</h3>

                                @if ($product->caracteristiques->isNotEmpty())
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%"> </th>
                                                <th style="width: 60%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->caracteristiques as $c)
                                                <tr>
                                                    <td class="bg-body-highlight align-middle">
                                                        <h6
                                                            class="mb-0 text-body text-uppercase fw-bolder px-4 fs-9 lh-sm">
                                                            {{ $c->name }}
                                                        </h6>
                                                    </td>
                                                    <td class="px-5 mb-0">
                                                        {{ $c->pivot->value }}
                                                        @if ($c->unite)
                                                            {{ $c->unite }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="ms-4 text-muted">Aucune spécification disponible pour ce produit.</p>
                                @endif

                            </div>

                            <div class="tab-pane fade {{ $tab === 'reviews' ? 'show active' : '' }}" id="tab-reviews"
                                role="tabpanel" aria-labelledby="reviews-tab">
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
                                        <div class="col-auto">
                                            <!-- Bouton pour ouvrir le modal -->
                                            <button type="button" class="btn btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#reviewModal">
                                                Write a review
                                            </button>

                                            <!-- Modale principale (téléportée dans <body> pour un affichage correct) -->
                                            @teleport('body')
                                            <div class="modal fade" id="reviewModal" tabindex="-1"
                                                aria-labelledby="reviewModalLabel" aria-hidden="true"
                                                wire:ignore.self>
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title" id="reviewModalLabel">Add your
                                                                review</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <form wire:submit.prevent="submitReview">
                                                            <div class="modal-body pt-2 pb-4">

                                                                <!-- Rater (5 étoiles) -->
                                                                <div class="mb-4 text-center">
                                                                    <div wire:ignore id="stars"
                                                                        class="rating-stars mb-2 fs-1 text-warning">
                                                                        ★ ★ ★ ★ ★
                                                                    </div>
                                                                    <div class="text-muted fs-7" hidden>
                                                                        Selected: <span
                                                                            id="rating-value">{{ $rating ?? 0 }} /
                                                                            5</span>
                                                                    </div>
                                                                    <input type="hidden" wire:model="rating"
                                                                        id="rating-input">
                                                                </div>

                                                                <!-- Commentaire -->
                                                                <div class="mb-4">
                                                                    <label class="form-label">Your review</label>
                                                                    <textarea wire:model.defer="commentaire" class="form-control" rows="4"
                                                                        placeholder="Tell us about your experience"></textarea>
                                                                </div>

                                                                <!-- Upload d'images -->
                                                                <div class="mb-4">
                                                                    <label class="form-label">Attach photos
                                                                        (optional)</label>
                                                                    <input type="file" wire:model="avisimages"
                                                                        accept="image/*" multiple
                                                                        class="form-control">
                                                                </div>

                                                            </div>
                                                            <div class="modal-footer border-0 pt-0">
                                                                <button type="button" class="btn btn-light"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary rounded-pill">
                                                                    <span>Submit Review</span>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endteleport


                                        </div>
                                    </div>
                                    @forelse($reviews as $review)
                                        <div class="mb-4 hover-actions-trigger btn-reveal-trigger">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="mb-2">
                                                    @for ($i = 1; $i <= 5; $i++)

                                                        <span
                                                            class="{{ ($i <= (int) ($review->nb_etoiles ?? 0)) ? 'fa fa-star' : 'fa-regular fa-star' }} text-warning"></span>
                                                    @endfor
                                                    <span class="text-body-secondary ms-1">par</span>
                                                    {{ optional($review->user)->name ?? 'Client' }}
                                                </h5>
                                            </div>
                                            <p class="text-body-tertiary fs-9 mb-1">
                                                {{ optional($review->created_at)->diffForHumans() }}
                                            </p>
                                            <p class="text-body-highlight mb-1">{{ $review->commentaire ?? '' }}</p>
                                            <div class="row g-2 mb-2">
                                                @if ($review->photos->isNotEmpty())
                                                    <div class="row g-2 mb-2">
                                                        @foreach ($review->photos as $photo)
                                                            <div class="col-auto">
                                                                <a href="{{ $photo->getImageUrl(800, 800) }}"
                                                                    data-gallery="gallery-{{ $review->id }}">
                                                                    <img src="{{ $photo->getImageUrl(164, 164) }}"
                                                                        alt="" height="164" />
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($review->response)
                                                <div class="d-flex">
                                                    <span class="fas fa-reply fa-rotate-180 me-2"></span>
                                                    <div>
                                                        <h5 class="fs-8 mb-0">
                                                            Respond from Admin
                                                            <span class="text-body-tertiary fs-9 ms-2">
                                                                {{ $review->response->created_at->diffForHumans() }}
                                                            </span>
                                                        </h5>
                                                        <p class="text-body-highlight mb-0">
                                                            {{ $review->response->message }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    @empty
                                        <p class="text-body-tertiary mb-0">Aucun avis pour le moment.</p>
                                    @endforelse

                                    {{ $reviews->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Section Bundle --}}
                    <div class="col-12 col-lg-5 col-xl-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="text-body-emphasis mb-3">🛒 Souvent achetés ensemble</h5>

                                <p class="text-body-secondary fs-8 mb-3">
                                    avec <strong>{{ $product->name }}</strong>
                                </p>

                                <div class="border-dashed border-y border-translucent py-4 mb-4">
                                    @forelse($bundleItems as $item)
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="form-check me-3">
                                                <input class="form-check-input bundle-checkbox" type="checkbox"
                                                    id="bundle-{{ $item->id }}"
                                                    wire:change="toggleBundleItem({{ $item->id }})"
                                                    {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="bundle-{{ $item->id }}"></label>
                                            </div>

                                            <div class="flex-grow-1">
                                                <a href="{{ route('produits.show', ['slug' => $item->getSlug(), 'id' => $item->id]) }}"
                                                    class="text-decoration-none">
                                                    <img src="{{ $item->getPhoto() ? $item->getPhoto()->getImageUrl(60, 60) : asset('assets/img/products/1.png') }}"
                                                        width="60" height="60" class="rounded shadow-sm me-3"
                                                        alt="{{ $item->name }}">
                                                    <div class="d-inline-block align-middle">
                                                        <div class="fw-semibold line-clamp-2 fs-9 mb-1">
                                                            {{ $item->name }}</div>
                                                        <div class="text-primary fw-bold">
                                                            {{ number_format($item->prix_promo ?? $item->price, 0, ',', ' ') }}
                                                            FCFA</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-muted py-4">Aucun produit complémentaire</p>
                                    @endforelse
                                </div>

                                {{-- Total DYNAMIQUE --}}
                                <div class="d-flex align-items-end justify-content-between pt-3">
                                    <div>
                                        <small class="text-muted mb-1 d-block">Total ({{ $selectedItemsCount }}
                                            articles)</small>
                                        <h3 class="mb-0 text-success fw-bold">
                                            {{ number_format($bundleTotal, 0, ',', ' ') }} FCFA</h3>
                                    </div>
                                    <button class="btn btn-warning px-4 py-2" wire:click="addBundleToCart"
                                        wire:loading.attr="disabled">
                                        Ajouter {{ $selectedItemsCount }} articles
                                        <i class="fas fa-shopping-cart ms-2"></i>
                                    </button>
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
                                <!-- Copie exacte de votre HTML avec variables dynamiques -->
                                <div class="position-relative text-decoration-none product-card h-100">
                                    <div class="d-flex flex-column justify-content-between h-100">
                                        <div>
                                            <div
                                                class="border border-1 border-translucent rounded-3 position-relative mb-3">
                                                <!-- ✅ Bouton Livewire qui ENREGISTRE DIRECT dans wishlist_user_produit -->
                                                @if (auth()->check())
                                                    {{-- ✅ CONNECTÉ : wire:click ACTIF --}}
                                                    <button wire:click="toggleWishlist({{ $p->id }})"
                                                        class="btn btn-wish btn-wish-primary z-2 p-2" tabindex="-1"
                                                        style="box-shadow: none; outline: none;"**
                                                        data-bs-toggle="tooltip" title="Wishlist">
                                                        <i
                                                            class="{{ auth()->user()->wishlistProducts->contains($p->id) ? 'fas fa-heart text-danger' : 'far fa-heart' }}"></i>
                                                    </button>
                                                @else
                                                    {{-- ❌ NON CONNECTÉ : bouton disabled --}}
                                                    <a class="btn btn-wish btn-wish-primary z-2 d-toggle-container"
                                                        href="{{ route('login') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Add to wishlist"><span
                                                            class="fas fa-heart d-block-hover"
                                                            data-fa-transform="down-1"></span><span
                                                            class="far fa-heart d-none-hover"
                                                            data-fa-transform="down-1"></span>
                                                    </a>
                                                @endif


                                                <img class="img-fluid"
                                                    src="{{ $p->getPhoto() ? $p->getPhoto()->getImageUrl(530, 530) : asset('assets/img/products/1.png') }}"
                                                    alt="{{ $p->name }}" />
                                                @if ($p->is_featured)
                                                    <span
                                                        class="badge text-bg-success fs-10 product-verified-badge">Featured<span
                                                            class="fas fa-star ms-1"></span></span>
                                                @endif
                                            </div>
                                            <a class="stretched-link"
                                                href="{{ route('produits.show', ['slug' => $p->getSlug(), 'id' => $p->id]) }}">
                                                <h6 class="mb-2 lh-sm line-clamp-3 product-name">
                                                    {{ Str::limit($p->name, 60) }}</h6>
                                            </a>
                                            <!-- Étoiles rating calculé -->
                                            <p class="fs-9">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span
                                                        class="fa fa-star {{ $i <= round($p->reviews->avg('nb_etoiles') ?? 5) ? 'text-warning' : 'fa-regular text-warning-light' }}"></span>
                                                @endfor
                                                <span
                                                    class="text-body-quaternary fw-semibold ms-1">({{ $p->reviews->count() }}
                                                    rated)</span>
                                            </p>
                                        </div>
                                        <div>
                                            @if ($p->sale_price)
                                                <p class="fs-9 text-body-highlight fw-bold mb-2">Promo spéciale</p>
                                                <div class="d-flex align-items-center mb-1">
                                                    <p class="me-2 text-body text-decoration-line-through mb-0">
                                                        {{ $this->formatFcfa($p->price) }}</p>
                                                    <h3 class="text-body-emphasis mb-0">
                                                        {{ $this->formatFcfa($p->sale_price) }} </h3>
                                                </div>
                                            @else
                                                <h3 class="text-body-emphasis">
                                                    {{ $this->formatFcfa($p->price) }} </h3>
                                            @endif
                                            <p class="text-body-tertiary fw-semibold fs-9 lh-1 mb-0">
                                                {{ $p->colors_count }}
                                                couleur{{ $p->colors_count > 1 ? 's' : '' }}</p>
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
        const shareBtn = document.getElementById('shareBtn');
        if (shareBtn) shareBtn.addEventListener('click', async () => {
            const productData = {
                title: '{{ $product->name }}',
                url: '{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}',
                text: 'Découvrez ce produit : {{ $product->name }}'
            };

            try {
                if (navigator.share) {
                    await navigator.share(productData);
                    showSuccess();
                } else {
                    // Fallback WhatsApp (le plus utilisé)
                    window.open(
                        `https://wa.me/?text=${encodeURIComponent(productData.text)} ${encodeURIComponent(productData.url)}`,
                        '_blank',
                        'width=600,height=400'
                    );
                }
            } catch (err) {
                console.log('Partage annulé ou erreur:', err);
            }
        });

        function showSuccess() {
            const btn = document.getElementById('shareBtn');
            const icon = btn.querySelector('i');
            const status = btn.querySelector('.share-status');

            icon.classList.add('d-none');
            status.classList.remove('d-none');

            setTimeout(() => {
                icon.classList.remove('d-none');
                status.classList.add('d-none');
            }, 1500);
        }
    </script>

    <script>
        function addToCartWithQty(productId) {
            // Récupère la vraie quantité du DOM
            const qtyInput = document.querySelector(`#qty-container-${productId} .qty-input`);
            const quantity = parseInt(qtyInput.value) || 1;

            // ENVOIE À LIVEWIRE ✅
            @this.call('addToCart', productId, quantity);
        }

        document.addEventListener('livewire:initialized', () => {
            const starsContainer = document.getElementById('stars');
            const ratingSpan = document.getElementById('rating-value');
            const ratingInput = document.getElementById('rating-input');

            if (!starsContainer || !ratingSpan || !ratingInput) return;

            // Transforme le texte en spans
            function initStars() {
                const content = starsContainer.textContent.trim();
                starsContainer.innerHTML = '';
                let count = 0;
                for (const char of content) {
                    if (char === '★') {
                        const span = document.createElement('span');
                        span.textContent = char;
                        span.dataset.value = count + 1; // 1, 2, 3, 4, 5
                        starsContainer.appendChild(span);
                        count++;
                    }
                }
            }

            initStars();

            // Affiche un nombre d’étoiles
            function showStars(n) {
                const spans = starsContainer.querySelectorAll('span');
                spans.forEach((span, i) => {
                    if (i < n) {
                        span.classList.add('selected');
                    } else {
                        span.classList.remove('selected');
                    }
                });
            }

            // Initialiser avec la valeur actuelle
            showStars(parseFloat(ratingSpan.textContent) || 0);

            // Mise à jour depuis Livewire (on reçoit { value: x })
            Livewire.on('rater::value', (data) => {
                const value = data?.value || 0;
                ratingSpan.textContent = value;
                ratingInput.value = value;
                showStars(value);
            });

            // Hover
            starsContainer.addEventListener('mouseover', (e) => {
                if (e.target.matches('span')) {
                    const value = parseInt(e.target.dataset.value);
                    showStars(value);
                }
            });

            // En dehors, revenir à la valeur actuelle
            starsContainer.addEventListener('mouseout', () => {
                showStars(parseFloat(ratingInput.value) || 0);
            });

            // Clic
            starsContainer.addEventListener('click', (e) => {
                if (e.target.matches('span')) {
                    const value = parseFloat(e.target.dataset.value); // 1, 2, 3, 4, 5
                    Livewire.dispatch('rater::value', {
                        value: value
                    }); // envoyé comme objet
                }
            });
        });

        document.addEventListener('livewire:initialized', () => {
            // Au clic sur Submit Review
            Livewire.on('submit-review', () => {
                const input = document.getElementById('avisimages-temp');
                @this.set('avisimages', input.files); // envoie les fichiers au composant
            });
        });
    </script>

</div>
