<div class="swiper-wrapper ">
    @forelse($topDeals as $produit)
        <div class="swiper-slide">
            <!-- Copie exacte de votre HTML avec variables dynamiques -->
            <div class="position-relative text-decoration-none product-card h-100 top-deals-card">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="product-img-box position-relative mb-3">
                            <!-- ✅ Bouton Livewire qui ENREGISTRE DIRECT dans wishlist_user_produit -->
                            @if (auth()->check())
                                {{-- ✅ CONNECTÉ : wire:click ACTIF --}}
                                <button wire:click="toggleWishlist({{ $produit->id }})"
                                    class="btn btn-wish btn-wish-primary z-2 p-2" tabindex="-1"
                                    style="box-shadow: none; outline: none;" data-bs-toggle="tooltip" title="Wishlist">
                                    <i
                                        class="{{ auth()->user()->wishlistProducts->contains($produit->id) ? 'fas fa-heart text-danger' : 'far fa-heart' }}"></i>
                                </button>
                                
                            @else
                                {{-- ❌ NON CONNECTÉ : bouton disabled --}}
                                <a class="btn btn-wish btn-wish-primary z-2 d-toggle-container" href="{{ route('login') }}" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Add to wishlist"><span
                                        class="fas fa-heart d-block-hover" data-fa-transform="down-1"></span><span
                                        class="far fa-heart d-none-hover" data-fa-transform="down-1"></span>
                                </a>
                            @endif


                            <img class="product-contained-img"
                                src="{{ $produit->getPhoto() ? $produit->getPhoto()->getImageUrl(530, 530) : asset('assets/img/products/1.png') }}"
                                alt="{{ $produit->name }}" />
                            @if ($produit->is_featured)
                                <span class="badge text-bg-success fs-10 product-verified-badge">Featured<span
                                        class="fas fa-star ms-1"></span></span>
                            @endif
                        </div>
                        <a class="stretched-link"
                            href="{{ route('produits.show', ['slug' => $produit->getSlug(), 'id' => $produit->id]) }}">
                            <h6 class="mb-2 lh-sm line-clamp-3 product-name">{{ Str::limit($produit->name, 60) }}</h6>
                        </a>
                        <!-- Étoiles rating calculé -->
                        <p class="fs-9">
                            @for ($i = 1; $i <= 5; $i++)
                                <span
                                    class="fa fa-star {{ $i <= round($produit->reviews->avg('nb_etoiles') ?? 5) ? 'text-warning' : 'fa-regular text-warning-light' }}"></span>
                            @endfor
                            <span class="text-body-quaternary fw-semibold ms-1">({{ $produit->reviews->count() }}
                                rated)</span>
                        </p>
                    </div>
                    <div>
                        @if ($produit->sale_price)
                            <p class="fs-9 text-body-highlight fw-bold mb-2">Promo spéciale</p>
                            <div class="d-flex align-items-center mb-1">
                                <p class="me-2 text-body text-decoration-line-through mb-0">
                                    {{ $this->formatFcfa($produit->price) }}</p>
                                <h3 class="text-body-emphasis mb-0">{{ $this->formatFcfa($produit->sale_price) }} </h3>
                            </div>
                        @else
                            <h3 class="text-body-emphasis">{{ $this->formatFcfa($produit->price) }} </h3>
                        @endif
                        <p class="text-body-tertiary fw-semibold fs-9 lh-1 mb-0">{{ $produit->colors_count }}
                            couleur{{ $produit->colors_count > 1 ? 's' : '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="swiper-slide text-center py-5">
            <p>Pas de promotions</p>
        </div>
    @endforelse
</div>
