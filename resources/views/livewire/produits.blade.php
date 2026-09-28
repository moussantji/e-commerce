<div>
    <div class="toolbar">
        <span style="font-size:13px;font-weight:600;color:var(--violet-800)">Affiner</span>
        <label><input type="checkbox" wire:model.live="promoOnly"> En promotion</label>
        <label><input type="checkbox" wire:model.live="filters.availability.in_stock"> Disponible</label>
        @auth
            <label><input type="checkbox" wire:model.live="favOnly"> Mes favoris</label>
        @endauth
        <input class="ctrl" style="border-radius:12px;min-width:170px" type="search" placeholder="Rechercher..."
            aria-label="Rechercher" wire:model.live.debounce.500ms="search">
        <span class="grow"></span>
        <label for="tri">Trier par</label>
        <select class="ctrl" id="tri" wire:model.live="sort" aria-label="Trier les produits">
            <option value="populaire">Popularité</option>
            <option value="prix-asc">Prix croissant</option>
            <option value="prix-desc">Prix décroissant</option>
            <option value="note">Meilleures notes</option>
        </select>
    </div>

    <div class="toolbar" style="margin-top:-6px">
        <label for="pmin">Prix min</label>
        <input class="ctrl" id="pmin" style="border-radius:12px;width:130px" type="number" placeholder="0"
            wire:model.live.debounce.500ms="filters.min_price">
        <label for="pmax">Prix max</label>
        <input class="ctrl" id="pmax" style="border-radius:12px;width:130px" type="number" placeholder="Max"
            wire:model.live.debounce.500ms="filters.max_price">
        <span class="grow"></span>
        <button class="btn-ghost-sm" type="button" wire:click="clearFilters">Effacer les filtres</button>
    </div>

    <div class="grid plist">
        @forelse($products as $product)
            @php
                $hasPromo = $product->sale_price && $product->sale_price < $product->price;
                $discount = $hasPromo && $product->price > 0 ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                $img = $product->getPhoto() ? $product->getPhoto()->getImageUrl(530, 530) : asset('assets/img/products/1.png');
                $inWishlist = in_array($product->id, $wishlistItems ?? []);
                $pUrl = route('produits.show', ['slug' => $product->getSlug(), 'id' => $product->id]);
            @endphp
            <article class="card rv in" wire:key="product-{{ $product->id }}">
                <div class="thumb" style="background-image:url('{{ $img }}')">
                    @if ($hasPromo)<span class="off">-{{ $discount }}%</span>@endif
                    @auth
                        <button class="fav {{ $inWishlist ? 'on' : '' }}"
                            wire:click="toggleWishlist({{ $product->id }})"
                            aria-label="{{ $inWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' }}"
                            title="Favori"><svg class="ic">
                                <use href="#i-heart" />
                            </svg><span class="tip">{{ $inWishlist ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span></button>
                    @else
                        <a class="fav" href="{{ route('login') }}" aria-label="Ajouter aux favoris" title="Favori"><svg class="ic">
                                <use href="#i-heart" />
                            </svg><span class="tip">Connectez-vous pour liker</span></a>
                    @endauth
                </div>
                <div class="body">
                    <div class="name">{{ Str::limit($product->name, 32) }}</div>
                    @if ($hasPromo)<div class="was">{{ $this->formatFcfa($product->price) }}</div>@endif
                    <div class="price">{{ $this->formatFcfa($hasPromo ? $product->sale_price : $product->price) }}</div>
                    <a href="{{ $pUrl }}" class="add">Voir le produit</a>
                </div>
            </article>
        @empty
            <div style="grid-column:1/-1">
                <div class="empty"><svg class="ic">
                        <use href="#i-search" />
                    </svg>
                    <h3>Aucun produit trouvé</h3>
                    <p>Essayez d'ajuster vos filtres pour voir plus de produits.</p>
                    <div class="pdp-actions" style="justify-content:center;margin-top:20px">
                        <button class="btn-solid" type="button" wire:click="clearFilters">Effacer les filtres</button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($hasMore ?? false)
        <div style="text-align:center;padding:24px 0" x-data x-intersect.margin.400px="$wire.loadMore()">
            <div wire:loading wire:target="loadMore" style="color:var(--mut);font-size:14px">Chargement des produits…</div>
            <div wire:loading.remove wire:target="loadMore" style="color:#9ca3af;font-size:13px">Faites défiler pour
                voir plus de produits</div>
        </div>
    @endif
</div>
