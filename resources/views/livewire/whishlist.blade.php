<div>
    <h2 class="mb-5">Wishlist<span class="text-body-tertiary fw-normal ms-2">({{ $wishlistCount }})</span></h2>

    {{-- ✅ TA STRUCTURE EXACTE --}}
    <div class="border-y border-translucent" id="productWishlistTable"
        data-list='{"valueNames":["products","color","size","price","quantity","total"],"page":5,"pagination":true}'>
        <div class="table-responsive scrollbar">
            <table class="table fs-9 mb-0">
                <thead>
                    {{-- TES TH IDENTIQUES --}}
                    <tr>
                        <th class="sort white-space-nowrap align-middle fs-10" scope="col" style="width:7%;"></th>
                        <th class="sort white-space-nowrap align-middle" scope="col" style="width:30%; min-width:250px;"
                            data-sort="products">PRODUCTS</th>
                        <th class="sort align-middle" scope="col" data-sort="color" style="width:16%;">COLOR</th>
                        <th class="sort align-middle" scope="col" data-sort="size" style="width:10%;">SIZE</th>
                        <th class="sort align-middle text-end" scope="col" data-sort="price" style="width:10%;">PRICE
                        </th>
                        <th class="sort align-middle text-end pe-0" scope="col" style="width:35%;"></th>
                    </tr>
                </thead>
                <tbody class="list" id="profile-wishlist-table-body">
                    @forelse($products as $product)
                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                            {{-- IMAGE --}}
                            <td class="align-middle white-space-nowrap ps-0 py-0">
                                <a class="border border-translucent rounded-2 d-inline-block"
                                    href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}">
                                    @if($product->getPhoto())
                                        <img src="{{ $product->getPhoto()->getImageUrl(53, 53) }}" width="53"
                                            alt="{{ $product->name }}" />
                                    @else
                                        <div style="width:53px;height:53px"
                                            class="bg-light rounded-2 d-flex align-items-center justify-content-center">
                                            <span class="fas fa-image text-muted"></span>
                                        </div>
                                    @endif
                                </a>
                            </td>

                            {{-- NOM --}}
                            <td class="products align-middle pe-11">
                                <a class="fw-semibold mb-0 line-clamp-1"
                                    href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}">
                                    {{ Str::limit($product->name, 60) }}
                                </a>
                            </td>

                            {{-- ✅ COULEUR depuis caracteristiques --}}
                            <td class="color align-middle white-space-nowrap fs-9 text-body">
                                {{ $product->caracteristiques->where('type', 'couleur')->first()?->pivot->value ?? 'N/A' }}
                            </td>

                            {{-- ✅ TAILLE depuis caracteristiques --}}
                            <td class="size align-middle white-space-nowrap text-body-tertiary fs-9 fw-semibold">
                                {{ $product->caracteristiques->where('type', 'taille')->first()?->pivot->value ?? 'N/A' }}
                            </td>

                            {{-- PRIX --}}
                            <td class="price align-middle text-body fs-9 fw-semibold text-end">
                                {{ number_format($product->sale_price ?? $product->price, 0) }}<span class="text-muted">
                                    FCFA</span>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="total align-middle fw-bold text-body-highlight text-end text-nowrap pe-0">
                                <button wire:click="removeFromWishlist({{ $product->id }})"
                                    class="btn btn-sm text-body-quaternary text-body-tertiary-hover me-2"
                                    wire:confirm="Retirer {{ $product->name }} ?">
                                    <span class="fas fa-trash"></span>
                                </button>
                                <button class="btn btn-primary fs-10" wire:click="addToCart({{ $product->id }})">
                                    <span class="fas fa-shopping-cart me-1 fs-10"></span>Add to cart
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Votre wishlist est vide
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- ✅ TA PAGINATION JS IDENTIQUE --}}
        @if ($products->count() > 0 || $products->isNotEmpty())
            <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
                    <a class="fw-semibold" href="#!" data-list-view="*">View all</a>
                </div>
                <div class="col-auto d-flex">
                    <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                    <ul class="mb-0 pagination"></ul>
                    <button class="page-link pe-0" data-list-pagination="next"><span
                            class="fas fa-chevron-right"></span></button>
                </div>
            </div>
        @endif

    </div>
</div>
