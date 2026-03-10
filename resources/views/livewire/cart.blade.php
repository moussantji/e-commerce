<div class="row g-5">
    <div class="col-12 col-lg-8">
        <div id="cartTable" data-list='{"valueNames":["products","color","size","price","quantity","total"],"page":10}'>
            <div class="table-responsive scrollbar mx-n1 px-1">
                @if ($panier && $itemsCount > 0)
                    <table class="table fs-9 mb-0 border-top border-translucent">
                        <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle fs-10" scope="col"></th>
                                <th class="sort white-space-nowrap align-middle" scope="col" style="min-width:250px;">
                                    PRODUCTS</th>
                                <th class="sort align-middle" scope="col" style="width:80px;">COLOR</th>
                                <th class="sort align-middle" scope="col" style="width:150px;">SIZE</th>
                                <th class="sort align-middle text-end" scope="col" style="width:300px;">PRICE</th>
                                <th class="sort align-middle ps-5" scope="col" style="width:200px;">QUANTITY</th>
                                <th class="sort align-middle text-end" scope="col" style="width:250px;">TOTAL</th>
                                <th class="sort text-end align-middle pe-0" scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="list" id="cart-table-body">
                            @if ($panier && $panier->products->count() > 0)
                                @foreach ($panier->products as $product)
                                    <tr class="cart-table-row btn-reveal-trigger">
                                        {{-- ✅ getPhoto() fonctionne ! --}}
                                        <td class="align-middle white-space-nowrap py-0">
                                            <a class="d-block border border-translucent rounded-2"
                                                href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}">
                                                @if ($product->getPhoto())
                                                    <img src="{{ $product->getPhoto()->getImageUrl(53, 53) }}"
                                                        alt="{{ $product->name }}" width="53" />
                                                @else
                                                    <img src="{{ asset('assets/img/products/' . $product->id . '.png') }}"
                                                        alt="{{ $product->name }}" width="53" />
                                                @endif
                                            </a>
                                        </td>

                                        {{-- ✅ getSlug() fonctionne ! --}}
                                        <td class="products align-middle">
                                            <a class="fw-semibold mb-0 line-clamp-2"
                                                href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}">
                                                {{ Str::limit($product->name, 80) }}
                                            </a>
                                        </td>

                                        <td class="color align-middle white-space-nowrap fs-9 text-body">
                                            {{ $product->couleur ?? 'Noir' }}
                                        </td>
                                        <td
                                            class="size align-middle white-space-nowrap text-body-tertiary fs-9 fw-semibold">
                                            {{ $product->taille ?? 'M' }}
                                        </td>

                                        {{-- Prix pivot --}}
                                        <td class="price align-middle text-body fs-9 fw-semibold text-end">
                                            {{-- ✅ 4 vérifications imbriquées --}}
                                            {{ $this->formatFcfa($product->pivot->prix_unitaire ?? 0) }}
                                        </td>

                                        <td class="quantity align-middle fs-8 ps-5">
                                            <div class="input-group input-group-sm flex-nowrap">
                                                <button class="btn btn-sm px-2"
                                                    wire:click="decreaseQuantity({{ $product->id }})"
                                                    {{ ($product->pivot?->quantite ?? 1) <= 1 ? 'hidden' : '' }}>-</button>
                                                <input
                                                    class="form-control text-center input-spin-none bg-transparent border-0 px-0"
                                                    type="number" min="1"
                                                    value="{{ $product->pivot?->quantite ?? 1 }}"
                                                    disabled />
                                                <button class="btn btn-sm px-2"
                                                    wire:click="increaseQuantity({{ $product->id }})">+</button>
                                            </div>
                                        </td>

                                        <td class="total align-middle fw-bold text-body-highlight text-end">
                                            {{ $this->formatFcfa($product->pivot?->total_ligne ?? 0) }}
                                        </td>

                                        <td class="align-middle white-space-nowrap text-end pe-0 ps-3">
                                            <button wire:click="removeFromCart({{ $product->id }})"
                                                class="btn btn-sm text-body-tertiary"
                                                onclick="return confirm('Supprimer {{ $product->name }} ?')">
                                                <span class="fas fa-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                                    <h5>Votre panier est vide</h5>
                                    <a href="{{ route('produits') }}" class="btn btn-primary">Voir les
                                        produits</a>
                                </div>
                            @endif

                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-body-emphasis fw-semibold ps-0 fs-8" colspan="6">Sous-total :</td>
                                <td class="text-body-emphasis fw-bold text-end fs-8">{{ $this->formatFcfa($total) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted mb-3">Votre panier est vide</h5>
                        <a href="{{ route('products') }}" class="btn btn-primary">
                            Voir les produits
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Summary -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-between-center mb-3">
                    <h3 class="card-title mb-0">Résumé</h3>
                    @if ($panier && $itemsCount > 0)
                        <span class="badge bg-success-soft">
                            {{ $itemsCount }} article{{ $itemsCount > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>

                {{-- Paiement --}}
                <select class="form-select" wire:model="paymentMethodId">
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}">
                            @if ($method->getPhoto())
                                <span class="me-2"
                                    style="background-image: url('{{ $method->getPhoto()->getImageUrl(20, 20) }}');
                                           width: 20px; height: 20px; display: inline-block;
                                           background-size: cover; background-position: center;
                                           border-radius: 3px;"></span>
                            @endif
                            {{ $method->name ?? $method->method_name }}
                            @if ($method->price ?? $method->frais > 0)
                                ({{ $this->formatFcfa($method->price ?? $method->frais) }})
                            @endif
                        </option>
                    @endforeach
                </select>

                {{-- Livraison --}}
                @if ($panier && $itemsCount > 0)
                    <select class="form-select mb-3 mt-3" wire:model.live="deliveryMethodId">
                        @foreach ($deliveryMethods as $method)
                            <option value="{{ $method->id }}">
                                {{ $method->method_name }} - {{ $this->formatFcfa($method->price) }}
                                {{ $this->formatDeliveryTime($method) ?? '' }}
                            </option>
                        @endforeach
                    </select>

                    <div class="border-start border-dashed ps-3 mb-3">
                        {{-- Sous-total --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total :</span>
                            <strong>{{ $this->formatFcfa($total) }}</strong>
                        </div>

                        {{-- Promo --}}
                        @if ($discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Promo <code>{{ $voucherCode }}</code> :</span>
                                <strong>-{{ $this->formatFcfa($discount) }}</strong>
                            </div>
                        @endif

                        {{-- 🔥 ERREUR PROMO (même style, rouge) --}}
                        @if ($voucherError)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Promo <code>{{ $voucherCode }}</code> :</span>
                                <strong class="fw-bold">{{ $voucherError }}</strong>
                            </div>
                        @endif

                        {{-- Livraison --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span>Livraison :</span>
                            <strong>{{ $this->formatFcfa($shippingCost) }}</strong>
                        </div>

                        {{-- Code promo --}}
                        <div class="input-group input-group-sm mb-3">
                            <input class="form-control" wire:model="voucherCode" placeholder="PROMO10, SOLDES20..." />
                            <button class="btn btn-outline-primary px-3" wire:click="applyVoucher"
                                wire:loading.attr="disabled" >
                                OK
                            </button>
                        </div>

                        {{-- TOTAL --}}
                        <hr class="my-3">
                        <div class="d-flex justify-content-between">
                            <h4 class="mb-0">TOTAL :</h4>
                            <h4 class="mb-0 text-primary fw-bold">
                                {{ $this->formatFcfa($this->getFinalTotal()) }}
                            </h4>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" wire:click="checkout" wire:loading.attr="disabled">
                        <i class="fas fa-lock me-2"></i>
                        Passer à la caisse
                        <i class="fas fa-chevron-right ms-2"></i>
                    </button>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Panier vide</h5>
                        <a href="{{ route('products') }}" class="btn btn-primary">
                            Voir les produits
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>



</div>
