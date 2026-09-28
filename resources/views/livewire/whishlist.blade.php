<div>
    <div class="panel">
        <h2><svg class="ic">
                <use href="#i-heart" />
            </svg> Mes favoris <span class="badge-nb">{{ $wishlistCount }}</span></h2>
        @forelse($products as $product)
            @php
                $img = $product->getPhoto()
                    ? $product->getPhoto()->getImageUrl(120, 120)
                    : asset('assets/img/products/1.png');
                $px = $product->sale_price && $product->sale_price < $product->price ? $product->sale_price : $product->price;
            @endphp
            <div class="dligne">
                <a class="th" style="background-image:url('{{ $img }}')"
                    href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}"
                    aria-label="{{ $product->name }}"></a>
                <div>
                    <b><a
                            href="{{ route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]) }}">{{ Str::limit($product->name, 60) }}</a></b>
                    <small>{{ number_format($px, 0, ',', ' ') }} FCFA ·
                        {{ $product->caracteristiques->where('type', 'couleur')->first()?->pivot->value ?? '' }}</small>
                </div>
                <div style="display:flex;gap:8px;align-items:center">
                    <button class="btn-ghost-sm" type="button" wire:click="addToCart({{ $product->id }})">+ Panier</button>
                    <button class="lien" type="button" wire:click="removeFromWishlist({{ $product->id }})"
                        wire:confirm="Retirer {{ $product->name }} ?">Retirer</button>
                </div>
            </div>
        @empty
            <div class="empty" style="border:0;margin:0"><svg class="ic">
                    <use href="#i-heart" />
                </svg>
                <h3>Aucun favori</h3>
                <p>Cliquez sur le cœur d'un produit pour l'enregistrer ici.</p>
                <div class="pdp-actions" style="justify-content:center;margin-top:14px">
                    <a class="btn-solid" href="{{ route('products') }}">Voir les produits</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
